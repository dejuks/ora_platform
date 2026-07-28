<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;

/**
 * The eBook Digital Library's public portal. Open Access titles are
 * visible and downloadable by anyone. Restricted titles are visible
 * (so readers know they exist) but require login to download.
 * Embargoed titles stay hidden entirely until the embargo lifts.
 *
 * Supports the same four query-string filters as the Journal public
 * portal, combinable:
 *   ?q=climate            free-text search (title/keywords)
 *   ?category=fiction     filter by category slug
 *   ?letter=B             A-Z filter, first letter of the title
 *   ?sort=az|za|latest    default: latest
 */
class PublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::published()
            ->where(function ($q) {
                $q->where('access_type', '!=', 'embargoed')
                    ->orWhereNull('embargo_until')
                    ->orWhere('embargo_until', '<=', now());
            })
            ->with(['author', 'category']);

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

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'az' => $query->orderBy('title'),
            'za' => $query->orderByDesc('title'),
            default => $query->latest('published_at'),
        };

        $books = $query->paginate(9)->withQueryString();

        $categories = BookCategory::active()
            ->withCount(['books' => fn ($q) => $q->published()])
            ->ordered()
            ->get();

        $letters = range('A', 'Z');

        return view('modules.ebook.public.index', compact('books', 'categories', 'letters'));
    }

    public function show(Book $book)
    {
        abort_unless($book->status === 'published', 404);
        abort_if($book->access_type === 'embargoed' && $book->embargo_until && $book->embargo_until->isFuture(), 404);

        $book->load(['author', 'category']);

        return view('modules.ebook.public.show', compact('book'));
    }
}
