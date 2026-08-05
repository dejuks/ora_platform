<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBookCopy;
use App\Models\LibraryBranch;
use App\Models\LibraryCirculationPolicy;
use App\Models\LibraryFine;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Notifications\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * The front-desk circulation workflow — the Librarian (Physical)
 * checks items in and out, renews on request, and collects fines on
 * a late return. A member can also renew their own active loan
 * (self-service), same as the physical workflow doc describes
 * ("Renew borrowed items ... online or in person").
 */
class CirculationController extends Controller
{
    /**
     * Staff view: every active/overdue loan. Members are redirected
     * to their own loan history instead.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user->hasModulePermission('library', 'manage-circulation') && ! $user->isSuperAdmin()) {
            $member = $user->libraryMember;

            if (! $member) {
                return redirect()->route('library.dashboard')
                    ->with('error', 'You do not have a library membership record yet — ask a Librarian to enroll you before you can borrow items.');
            }

            return redirect()->route('library.members.show', $member);
        }

        $query = LibraryLoan::with(['copy.book', 'copy.branch', 'member.user'])->latest('checked_out_at');

        $accessibleBranchIds = $user->accessibleLibraryBranchIds();

        if ($accessibleBranchIds !== null) {
            $query->whereHas('copy', fn ($q) => $q->whereIn('branch_id', $accessibleBranchIds));
        }

        if ($request->filled('branch')) {
            $query->whereHas('copy', fn ($q) => $q->where('branch_id', $request->get('branch')));
        }

        if ($request->get('status') === 'overdue') {
            $query->overdue();
        } elseif ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        $loans = $query->paginate(20)->withQueryString();

        $branches = $accessibleBranchIds === null
            ? LibraryBranch::active()->orderBy('name')->get()
            : LibraryBranch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get();

        return view('modules.library.circulation.index', compact('loans', 'branches'));
    }

    /**
     * Librarian: check out an available copy to a member by barcode
     * + membership number.
     */
    public function checkout(Request $request)
    {
        $this->authorizePermission('manage-circulation');

        $data = $request->validate([
            'barcode' => ['required', 'string', 'exists:library_book_copies,barcode'],
            'membership_no' => ['required', 'string', 'exists:library_members,membership_no'],
        ]);

        $copy = LibraryBookCopy::where('barcode', $data['barcode'])->firstOrFail();
        $member = LibraryMember::where('membership_no', $data['membership_no'])->firstOrFail();

        abort_unless(Auth::user()->canAccessLibraryBranch($copy->branch_id), 403, 'You are not assigned to this copy\'s branch.');

        // A copy reserved (on_hold) for this exact member's ready hold
        // can also be checked out — anyone else's reservation, or a
        // copy that's simply out on loan, cannot.
        $readyHold = $copy->status === 'on_hold'
            ? $copy->holds()->where('library_member_id', $member->id)->where('status', 'ready')->first()
            : null;

        abort_unless($copy->isAvailable() || $readyHold, 422, 'This copy is not available ('.$copy->statusLabel().').');
        abort_unless($member->canBorrow(), 422, 'This member cannot borrow right now (limit reached, suspended, or unpaid fines).');

        $policy = LibraryCirculationPolicy::current();

        $loan = DB::transaction(function () use ($copy, $member, $policy, $readyHold) {

            $copy->update(['status' => 'on_loan', 'updated_by' => Auth::id()]);

            if ($readyHold) {
                $readyHold->update(['status' => 'fulfilled']);
            }

            return LibraryLoan::create([
                'library_book_copy_id' => $copy->id,
                'library_member_id' => $member->id,
                'issued_by' => Auth::id(),
                'checked_out_at' => now(),
                'due_at' => now()->addDays($policy->loan_period_days),
                'status' => 'active',
            ]);
        });

        $member->user?->notify(new AppNotification(
            title: 'Item checked out',
            message: "\"{$copy->book->title}\" was checked out to you, due {$loan->due_at->format('M d, Y')}.",
            url: route('library.members.show', $member),
            icon: 'bi-journal-arrow-up',
            type: 'info',
        ));

        return redirect()
            ->route('library.circulation.index')
            ->with('success', "Checked out to {$member->membership_no}, due {$loan->due_at->format('M d, Y')}.");
    }
    /**
     * Librarian: check an item back in. Generates a fine
     * automatically if it came back late, and — if someone is
     * waiting on a hold — marks the copy on_hold instead of
     * available so the Holds queue can fulfill it.
     */
    public function checkin(LibraryLoan $loan)
    {
        $this->authorizePermission('manage-circulation');

        abort_unless($loan->status === 'active', 422, 'This loan has already been closed out.');

        abort_unless(Auth::user()->canAccessLibraryBranch($loan->copy->branch_id), 403, 'You are not assigned to this copy\'s branch.');

        $daysLate = $loan->daysOverdue();

        DB::transaction(function () use ($loan, $daysLate) {

            $loan->update([
                'status' => 'returned',
                'returned_at' => now(),
                'returned_to' => Auth::id(),
            ]);

            if ($daysLate > 0) {
                $policy = LibraryCirculationPolicy::current();

                LibraryFine::create([
                    'library_loan_id' => $loan->id,
                    'library_member_id' => $loan->library_member_id,
                    'amount' => round($daysLate * (float) $policy->fine_per_day, 2),
                    'days_overdue' => $daysLate,
                    'status' => 'unpaid',
                ]);
            }

            $hasWaitingHold = $loan->copy->book->holds()->where('status', 'pending')->exists();

            $loan->copy->update([
                'status' => $hasWaitingHold ? 'on_hold' : 'available',
                'updated_by' => Auth::id(),
            ]);
        });

        if ($daysLate > 0) {
            $loan->member->user?->notify(new AppNotification(
                title: 'Late fine issued',
                message: "A fine was recorded for returning \"{$loan->copy->book->title}\" {$daysLate} day(s) late.",
                url: route('library.fines.index'),
                icon: 'bi-exclamation-circle',
                type: 'warning',
            ));
        }

        return back()->with('success', 'Item checked in.'.($daysLate > 0 ? ' A late fine was recorded.' : ''));
    }

    /**
     * Renew an active loan — the Librarian can renew any loan;
     * a member can renew their own.
     */
    public function renew(LibraryLoan $loan)
    {
        $user = Auth::user();

        $isStaff = $user->isSuperAdmin() || $user->hasModulePermission('library', 'manage-circulation');
        $isOwner = $user->libraryMember?->id === $loan->library_member_id;

        abort_unless($isStaff || $isOwner, 403, 'You do not have permission to renew this loan.');

        if ($isStaff && ! $isOwner) {
            abort_unless(Auth::user()->canAccessLibraryBranch($loan->copy->branch_id), 403, 'You are not assigned to this copy\'s branch.');
        }

        $policy = LibraryCirculationPolicy::current();

        abort_unless($loan->canRenew($policy->max_renewals), 422, 'This loan cannot be renewed (limit reached or someone is holding this title).');

        $loan->update([
            'due_at' => $loan->due_at->addDays($policy->loan_period_days),
            'renewal_count' => $loan->renewal_count + 1,
        ]);

        if ($isStaff && ! $isOwner) {
            $loan->member->user?->notify(new AppNotification(
                title: 'Loan renewed',
                message: "Your loan for \"{$loan->copy->book->title}\" was renewed. New due date: {$loan->due_at->format('M d, Y')}.",
                url: route('library.members.show', $loan->library_member_id),
                icon: 'bi-arrow-clockwise',
                type: 'info',
            ));
        }

        return back()->with('success', 'Loan renewed. New due date: '.$loan->due_at->format('M d, Y').'.');
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization helpers
    |--------------------------------------------------------------------------
    */

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
