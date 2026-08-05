<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\JournalCategory;
use App\Models\Manuscript;
use Illuminate\Http\Request;

/**
 * The Journal's public portal. No authentication required — every
 * published manuscript is visible to any visitor, exactly like a
 * journal's public article listing / abstract page.
 *
 * Supports three query-string filters, combinable:
 *   ?q=climate            free-text search (title/keywords)
 *   ?category=fiction     filter by category slug
 *   ?letter=B             A-Z filter, first letter of the title
 *   ?sort=az|za|latest    default: az
 */
class PublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Manuscript::published()->with(['author', 'category', 'coAuthors']);

        if ($request->filled('q')) {
            $term = $request->string('q');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('keywords', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category')) {
            $query->inCategory($request->string('category'));
        }

        if ($request->filled('letter') && $request->get('letter') !== 'ALL') {
            $query->titleStartsWith(strtoupper($request->string('letter')));
        }

        $sort = $request->get('sort', 'az');
        match ($sort) {
            'za' => $query->orderByDesc('title'),
            'latest' => $query->latest('published_at'),
            default => $query->orderBy('title'),
        };

        $articles = $query->paginate(9)->withQueryString();

        $categories = JournalCategory::active()
            ->withCount(['manuscripts' => fn ($q) => $q->published()])
            ->ordered()
            ->get();

        $letters = range('A', 'Z');

        return view('modules.journal.public.index', compact('articles', 'categories', 'letters'));
    }

    public function show(Manuscript $manuscript)
    {
        abort_unless($manuscript->status === 'published', 404);

        $manuscript->load(['author', 'category', 'coAuthors']);

        return view('modules.journal.public.show', compact('manuscript'));
    }
}
