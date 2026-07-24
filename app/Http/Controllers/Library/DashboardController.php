<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryDigitalResource;
use App\Models\LibraryFine;
use App\Models\LibraryHold;
use App\Models\LibraryLoan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->libraryMember;
        $isCirculationStaff = $user->isSuperAdmin() || $user->hasModulePermission('library', 'manage-circulation');
        $canManageDigital = $user->hasModulePermission('library', 'manage-digital-collection');

        $stats = [
            'my_active_loans' => $member ? $member->loans()->where('status', 'active')->count() : null,
            'my_holds' => $member ? $member->holds()->whereIn('status', ['pending', 'ready'])->count() : null,
            'my_unpaid_fines' => $member ? $member->fines()->where('status', 'unpaid')->count() : null,
            'active_loans' => $isCirculationStaff ? LibraryLoan::where('status', 'active')->count() : null,
            'overdue_loans' => $isCirculationStaff ? LibraryLoan::overdue()->count() : null,
            'pending_holds' => $isCirculationStaff ? LibraryHold::where('status', 'pending')->count() : null,
            'pending_acquisitions' => ($user->hasModulePermission('library', 'approve-acquisitions')
                    || $user->hasModulePermission('library', 'manage-acquisitions')
                    || $user->hasModulePermission('library', 'catalog-items'))
                ? LibraryBook::where('status', 'pending_acquisition')->count()
                : null,
            'digital_drafts' => $canManageDigital ? LibraryDigitalResource::where('status', 'draft')->count() : null,
            'digital_published' => LibraryDigitalResource::where('status', 'published')->count(),
        ];

        return view('modules.library.dashboard', [
            'moduleLabel' => 'Library Management',
            'stats' => $stats,
            'hasMemberRecord' => (bool) $member,
        ]);
    }

    public function admin()
    {
        $stats = [
            'total_titles' => LibraryBook::count(),
            'active_titles' => LibraryBook::where('status', 'active')->count(),
            'pending_acquisitions' => LibraryBook::where('status', 'pending_acquisition')->count(),
            'active_loans' => LibraryLoan::where('status', 'active')->count(),
            'overdue_loans' => LibraryLoan::overdue()->count(),
            'pending_holds' => LibraryHold::where('status', 'pending')->count(),
            'unpaid_fines' => LibraryFine::where('status', 'unpaid')->count(),
            'digital_total' => LibraryDigitalResource::count(),
            'digital_drafts' => LibraryDigitalResource::where('status', 'draft')->count(),
            'digital_published' => LibraryDigitalResource::where('status', 'published')->count(),
        ];

        return view('modules.library.admin-dashboard', [
            'moduleLabel' => 'Library Management',
            'stats' => $stats,
        ]);
    }
}
