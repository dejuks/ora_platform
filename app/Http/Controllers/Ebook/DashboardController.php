<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $isEditorial = $user->isSuperAdmin()
            || $user->hasModulePermission('ebook', 'make-editorial-decision')
            || $user->hasModulePermission('ebook', 'screen-manuscripts')
            || $user->hasModulePermission('ebook', 'convert-and-publish-ebook')
            || $user->hasModulePermission('ebook', 'manage-payments');

        $stats = [
            'my_submissions' => Book::where('author_id', $user->id)->count(),
            'awaiting_my_review' => Book::whereHas('reviews', function ($q) use ($user) {
                $q->where('reviewer_id', $user->id)->where('status', '!=', 'submitted');
            })->count(),
            'total_books' => $isEditorial ? Book::count() : null,
            'awaiting_screening' => $user->hasModulePermission('ebook', 'screen-manuscripts')
                ? Book::where('status', 'submitted')->count()
                : null,
            'awaiting_clearance' => $user->hasModulePermission('ebook', 'manage-payments')
                ? Book::where('status', 'financial_clearance')->count()
                : null,
            'in_production' => $user->hasModulePermission('ebook', 'convert-and-publish-ebook')
                ? Book::where('status', 'in_production')->count()
                : null,
        ];

        return view('modules.ebook.dashboard', [
            'moduleLabel' => 'Ebook',
            'stats' => $stats,
            'canBecomeAuthor' => ! $user->hasModulePermission('ebook', 'submit-manuscript'),
        ]);
    }

    public function admin()
    {
        $stats = [
            'total_books' => Book::count(),
            'awaiting_screening' => Book::where('status', 'submitted')->count(),
            'under_review' => Book::where('status', 'under_review')->count(),
            'awaiting_clearance' => Book::where('status', 'financial_clearance')->count(),
            'in_production' => Book::where('status', 'in_production')->count(),
            'published' => Book::where('status', 'published')->count(),
        ];

        return view('modules.ebook.admin-dashboard', [
            'moduleLabel' => 'Ebook',
            'stats' => $stats,
        ]);
    }
}
