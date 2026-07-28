<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryFine;
use App\Notifications\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Fines are generated automatically on a late return (see
 * CirculationController::return). The Librarian (Physical)
 * "collects fines and fees" per the workflow doc; a member can view
 * their own.
 */
class FineController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = LibraryFine::with(['member.user', 'loan.copy.book'])->latest();

        if (! $user->hasModulePermission('library', 'manage-circulation') && ! $user->isSuperAdmin()) {
            $member = $user->libraryMember;

            if (! $member) {
                return redirect()->route('library.dashboard')
                    ->with('error', 'You do not have a library membership record yet — ask a Librarian to enroll you before you can view fines.');
            }

            $query->where('library_member_id', $member->id);
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        $fines = $query->paginate(20)->withQueryString();

        return view('modules.library.fines.index', compact('fines'));
    }

    public function pay(LibraryFine $fine)
    {
        $this->authorizePermission('manage-circulation');

        abort_unless($fine->status === 'unpaid', 422, 'This fine is already settled.');

        $fine->update([
            'status' => 'paid',
            'collected_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        $fine->member->user?->notify(new AppNotification(
            title: 'Fine payment received',
            message: "Your fine of {$fine->amount} for \"{$fine->loan->copy->book->title}\" was marked as paid.",
            url: route('library.fines.index'),
            icon: 'bi-receipt',
            type: 'success',
        ));

        return back()->with('success', 'Fine marked as paid.');
    }

    public function waive(Request $request, LibraryFine $fine)
    {
        $this->authorizePermission('manage-circulation');

        abort_unless($fine->status === 'unpaid', 422, 'This fine is already settled.');

        $data = $request->validate([
            'waiver_reason' => ['required', 'string', 'max:500'],
        ]);

        $fine->update([
            'status' => 'waived',
            'waived_by' => Auth::id(),
            'waiver_reason' => $data['waiver_reason'],
        ]);

        $fine->member->user?->notify(new AppNotification(
            title: 'Fine waived',
            message: "Your fine of {$fine->amount} for \"{$fine->loan->copy->book->title}\" was waived.",
            url: route('library.fines.index'),
            icon: 'bi-check-circle',
            type: 'success',
        ));

        return back()->with('success', 'Fine waived.');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
