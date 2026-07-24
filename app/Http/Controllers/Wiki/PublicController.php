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
        $query = Article::published()->latest('updated_at');

        if ($search = $request->query('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $articles = $query->paginate(15)->withQueryString();

        return view('modules.wiki.public.index', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_unless($article->status === 'published', 404);

        return view('modules.wiki.public.show', compact('article'));
    }
}
