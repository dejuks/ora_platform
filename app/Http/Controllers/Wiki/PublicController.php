<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

/**
 * A published wiki article is public record — anyone can browse and
 * read it, logged in or not, same reasoning as the Journal / Ebook
 * public portals.
 */
class PublicController extends Controller
{
    public function about()
    {
        return view('modules.wiki.public.about');
    }

    public function random()
    {
        $article = Article::whereNotNull('published_at')
            ->inRandomOrder()
            ->first();

        if (! $article) {
            return redirect()
                ->route('wiki.public.index')
                ->with('status', 'No published articles yet.');
        }

        return redirect()->route('wiki.public.show', $article);
    }

    public function index(Request $request)
    {
        $query = Article::published()->with('categories')->latest('updated_at');

        if ($search = $request->query('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categorySlug = $request->query('category')) {
            $query->inCategory($categorySlug);
        }

        $articles = $query->paginate(15)->withQueryString();
        $categories = \App\Models\WikiCategory::active()->ordered()->withCount([
            'articles' => fn ($q) => $q->published(),
        ])->get();

        return view('modules.wiki.public.index', compact('articles', 'categories'));
    }

    public function category(\App\Models\WikiCategory $category)
    {
        abort_unless($category->is_active, 404);

        $articles = $category->articles()
            ->published()
            ->latest('updated_at')
            ->paginate(15);

        $categories = \App\Models\WikiCategory::active()->ordered()->withCount([
            'articles' => fn ($q) => $q->published(),
        ])->get();

        return view('modules.wiki.public.category', compact('category', 'articles', 'categories'));
    }

    public function show(Article $article)
    {
        abort_unless($article->status === 'published', 404);

        $article->load('categories');

        return view('modules.wiki.public.show', compact('article'));
    }
}
