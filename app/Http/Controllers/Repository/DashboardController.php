<?php

namespace App\Http\Controllers\Repository;

use App\Http\Controllers\Controller;
use App\Models\RepositoryItem;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $isStaff = $user->isSuperAdmin()
            || $user->hasModulePermission('repository', 'curate-metadata')
            || $user->hasModulePermission('repository', 'review-repository-submissions')
            || $user->hasModulePermission('repository', 'approve-repository-submissions');

        $stats = [
            'my_deposits' => RepositoryItem::where('depositor_id', $user->id)->count(),
            'awaiting_metadata_review' => $user->hasModulePermission('repository', 'curate-metadata')
                ? RepositoryItem::whereIn('status', ['submitted', 'metadata_review'])->count()
                : null,
            'awaiting_content_review' => $user->hasModulePermission('repository', 'review-repository-submissions')
                ? RepositoryItem::where('status', 'content_review')->count()
                : null,
            'awaiting_final_decision' => $user->hasModulePermission('repository', 'approve-repository-submissions')
                ? RepositoryItem::where('status', 'recommended')->count()
                : null,
            'total_items' => $isStaff ? RepositoryItem::count() : null,
            'published_items' => $isStaff ? RepositoryItem::published()->count() : null,
        ];

        return view('modules.repository.dashboard', [
            'moduleLabel' => 'Repository Management',
            'stats' => $stats,
        ]);
    }

    public function admin()
    {
        $stats = [
            'total_items' => RepositoryItem::count(),
            'published_items' => RepositoryItem::published()->count(),
            'open_access' => RepositoryItem::published()->where('access_level', 'open')->count(),
            'restricted' => RepositoryItem::published()->where('access_level', 'restricted')->count(),
            'pending_review' => RepositoryItem::whereNotIn('status', ['published', 'rejected'])->count(),
            'rejected' => RepositoryItem::where('status', 'rejected')->count(),
            'total_downloads' => RepositoryItem::sum('downloads_count'),
            'by_resource_type' => RepositoryItem::selectRaw('resource_type, count(*) as total')
                ->groupBy('resource_type')
                ->pluck('total', 'resource_type'),
            'by_status' => RepositoryItem::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ];

        return view('modules.repository.admin-dashboard', [
            'moduleLabel' => 'Repository Management',
            'stats' => $stats,
        ]);
    }
}
