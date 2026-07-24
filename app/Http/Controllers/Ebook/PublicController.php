<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

/**
 * The eBook Digital Library's public portal. Open Access titles are
 * visible and downloadable by anyone. Restricted titles are visible
 * (so readers know they exist) but require login to download.
 * Embargoed titles stay hidden entirely until the embargo lifts.
 */
class PublicController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::published()
            ->where(function ($q) {
                $q->where('access_type', '!=', 'embargoed')
                    ->orWhereNull('embargo_until')
                    ->orWhere('embargo_until', '<=', now());
            })
            ->with('author')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('keywords', 'like', "%{$term}%");
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('modules.ebook.public.index', compact('books'));
    }

    public function show(Book $book)
    {
        abort_unless($book->status === 'published', 404);
        abort_if($book->access_type === 'embargoed' && $book->embargo_until && $book->embargo_until->isFuture(), 404);

        $book->load('author');

        return view('modules.ebook.public.show', compact('book'));
    }
}
