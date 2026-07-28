<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryCirculationPolicy;
use App\Models\LibraryHold;
use App\Notifications\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * A hold reserves a title (not a specific copy) for a member when
 * every copy is currently out — "Request/reserve/renew items" per
 * the Member workflow, "Handle holds/reservations" per the
 * Librarian's.
 */
class HoldController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = LibraryHold::with(['book', 'member.user'])->latest('requested_at');

        if (! $user->hasModulePermission('library', 'manage-circulation') && ! $user->isSuperAdmin()) {
            $member = $user->libraryMember;

            if (! $member) {
                return redirect()->route('library.dashboard')
                    ->with('error', 'You do not have a library membership record yet — ask a Librarian to enroll you before you can place holds.');
            }

            $query->where('library_member_id', $member->id);
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        $holds = $query->paginate(20)->withQueryString();

        return view('modules.library.holds.index', compact('holds'));
    }

    /**
     * Member: place a hold on a title with no available copies.
     */
    public function store(LibraryBook $book)
    {
        $user = Auth::user();
        $member = $user->libraryMember;

        if (! $member) {
            return back()->with('error', 'You do not have a library membership record yet — ask a Librarian to enroll you before you can place holds.');
        }

        abort_unless($member->status === 'active', 403, 'Your membership is not active.');
        abort_if($book->hasAvailableCopy(), 422, 'A copy is available — no need to place a hold, just check it out.');

        $exists = LibraryHold::where('library_book_id', $book->id)
            ->where('library_member_id', $member->id)
            ->whereIn('status', ['pending', 'ready'])
            ->exists();

        abort_if($exists, 422, 'You already have a hold on this title.');

        LibraryHold::create([
            'library_book_id' => $book->id,
            'library_member_id' => $member->id,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return back()->with('success', 'Hold placed. You will be notified when a copy is ready.');
    }

    /**
     * Librarian: a copy just came back for this title — reserve it
     * for the oldest pending hold in the queue.
     */
    public function fulfill(LibraryHold $hold)
    {
        $this->authorizePermission('manage-circulation');

        abort_unless($hold->status === 'pending', 422, 'This hold is not awaiting fulfillment.');

        // A copy already flagged on_hold might already be reserved
        // for someone else's 'ready' hold — only pick one that isn't,
        // otherwise two members could end up pointed at the same copy.
        $reservedCopyIds = LibraryHold::where('status', 'ready')->pluck('library_book_copy_id')->filter();

        $copy = $hold->book->copies()->where('status', 'on_hold')->whereNotIn('id', $reservedCopyIds)->first()
            ?? $hold->book->copies()->available()->first();

        abort_unless($copy, 422, 'No copy is available to fulfill this hold yet.');

        $policy = LibraryCirculationPolicy::current();

        DB::transaction(function () use ($hold, $copy, $policy) {
            $copy->update(['status' => 'on_hold', 'updated_by' => Auth::id()]);

            $hold->update([
                'library_book_copy_id' => $copy->id,
                'status' => 'ready',
                'ready_at' => now(),
                'expires_at' => now()->addDays($policy->hold_expiry_days),
            ]);
        });

        $hold->member->user?->notify(new AppNotification(
            title: 'Your hold is ready for pickup',
            message: "\"{$hold->book->title}\" is ready for pickup. Please collect it by {$hold->fresh()->expires_at->format('M j, Y')}.",
            url: route('library.holds.index'),
            icon: 'bi-bookmark-check',
            type: 'success',
        ));

        return back()->with('success', 'Hold marked ready for pickup, copy '.$copy->barcode.' reserved.');
    }

    public function cancel(LibraryHold $hold)
    {
        $user = Auth::user();
        $isOwner = $user->libraryMember?->id === $hold->library_member_id;

        abort_unless($isOwner || $user->isSuperAdmin() || $user->hasModulePermission('library', 'manage-circulation'), 403);

        abort_unless(in_array($hold->status, ['pending', 'ready']), 422, 'This hold can no longer be cancelled.');

        DB::transaction(function () use ($hold) {
            if ($hold->status === 'ready' && $hold->reservedCopy) {
                $hold->reservedCopy->update(['status' => 'available', 'updated_by' => Auth::id()]);
            }

            $hold->update(['status' => 'cancelled']);
        });

        if (! $isOwner) {
            $hold->member->user?->notify(new AppNotification(
                title: 'Hold cancelled',
                message: "Your hold on \"{$hold->book->title}\" was cancelled by library staff.",
                url: route('library.holds.index'),
                icon: 'bi-x-circle',
                type: 'warning',
            ));
        }

        return back()->with('success', 'Hold cancelled.');
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
