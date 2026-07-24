<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'my_submissions' => Manuscript::where('author_id', $user->id)->count(),
            'awaiting_my_review' => Manuscript::whereHas('reviews', function ($q) use ($user) {
                $q->where('reviewer_id', $user->id)->where('status', '!=', 'submitted');
            })->count(),
            'total_manuscripts' => $user->hasModulePermission('journal', 'screen-submissions')
                || $user->hasModulePermission('journal', 'make-final-decision')
                || $user->hasModulePermission('journal', 'manage-workflow')
                ? Manuscript::count()
                : null,
        ];

        return view('modules.journal.dashboard', [
            'moduleLabel' => 'Journal Management',
            'stats' => $stats,
        ]);
    }

    public function admin()
    {
        return view('modules.journal.admin-dashboard', [
            'moduleLabel' => 'Journal Management',
        ]);
    }
}
