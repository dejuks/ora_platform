<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleDeletionDiscussion;
use App\Models\Wiki\ContactMessage;

/**
 * Dashboard for the Oromo Wikipedia module.
 */
class DashboardController extends Controller
{
    public function index()
    {
        return view('modules.wiki.dashboard', [
            'moduleLabel' => 'Oromo Wikipedia',
        ]);
    }

    public function admin()
    {
        $stats = [
            'total_articles' => Article::count(),
            'published_articles' => Article::published()->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'protected_articles' => Article::where('protection_level', '!=', 'none')->count(),
            'trashed_articles' => Article::onlyTrashed()->count(),
            'open_deletion_discussions' => ArticleDeletionDiscussion::where('status', 'open')->count(),
            'unread_contact_messages' => ContactMessage::whereNull('read_at')->count(),
            'total_contact_messages' => ContactMessage::count(),
        ];

        $articlesByStatus = [
            'labels' => ['Published', 'Draft'],
            'data' => [$stats['published_articles'], $stats['draft_articles']],
        ];

        return view('modules.wiki.admin-dashboard', [
            'moduleLabel' => 'Oromo Wikipedia',
            'stats' => $stats,
            'articlesByStatus' => $articlesByStatus,
        ]);
    }
}
