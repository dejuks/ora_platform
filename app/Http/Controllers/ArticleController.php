<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\JournalCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Public journal/articles listing with A-Z and category filters.
     */
    public function index(Request $request)
    {
        $query = Article::query()->with('category');

        // Only published/publicly-visible articles.
        if (method_exists(Article::class, 'scopePublished')) {
            $query->published();
        }

        // Category filter: /journal/articles?category=fiction
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->string('category'));
            });
        }

        // A-Z letter filter: /journal/articles?letter=A
        if ($request->filled('letter') && $request->get('letter') !== 'ALL') {
            $letter = strtoupper($request->string('letter'));
            $query->where('title', 'like', $letter . '%');
        }

        // Search box (optional, works alongside the filters above)
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->string('q') . '%');
        }

        $sort = $request->get('sort', 'az'); // az | za | latest
        match ($sort) {
            'za' => $query->orderByDesc('title'),
            'latest' => $query->latest(),
            default => $query->orderBy('title'),
        };

        $articles = $query->paginate(15)->withQueryString();

        $categories = JournalCategory::where('is_active', true)
            ->withCount('articles')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $letters = range('A', 'Z');

        return view('journal.articles.index', compact('articles', 'categories', 'letters'));
    }

    public function show(Article $article)
    {
        $article->load('category');

        return view('journal.articles.show', compact('article'));
    }
}
