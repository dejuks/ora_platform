<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The Oromo Wikipedia editing workflow:
 *
 *   Registered Editor creates/edits an article (every save is logged
 *   as a revision) -> Administrator (Sysop) can protect/unprotect a
 *   page, or delete/restore it entirely -> Oversighter can suppress
 *   any individual revision that leaked private data.
 *
 * Every action below is gated by the actual permission that role
 * carries in this module (seeded in RoleSeeder), not by job title.
 */
class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['author', 'lastEditedBy'])
            ->withTrashed()
            ->latest('updated_at');

        if ($search = $request->query('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $articles = $query->paginate(15)->withQueryString();

        return view('modules.wiki.articles.index', compact('articles'));
    }

    public function create()
    {
        $this->authorizePermission('edit-articles');

        return view('modules.wiki.articles.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('edit-articles');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'edit_summary' => ['nullable', 'string', 'max:255'],
        ]);

        $article = DB::transaction(function () use ($data, $request) {

            $article = Article::create([
                'title' => $data['title'],
                'slug' => $this->uniqueSlug($data['title']),
                'content' => $data['content'],
                'status' => 'published',
                'author_id' => Auth::id(),
                'last_edited_by' => Auth::id(),
                'published_at' => now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->recordRevision($article, $data['content'], $data['edit_summary'] ?? 'Created the article.', $request);

            return $article;
        });

        return redirect()
            ->route('wiki.articles.show', $article)
            ->with('success', 'Article created.');
    }

    public function show(Article $article)
    {
        $article = Article::withTrashed()->with(['author', 'lastEditedBy', 'protectedBy'])->findOrFail($article->id);

        $article->load(['revisions' => function ($q) {
            $q->where('is_suppressed', false)->with('editor');
        }]);

        $openDiscussion = $article->openDeletionDiscussion()->with(['openedBy', 'comments.user'])->first();

        return view('modules.wiki.articles.show', compact('article', 'openDiscussion'));
    }

    public function edit(Article $article)
    {
        $this->authorizeEdit($article);

        return view('modules.wiki.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorizeEdit($article);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'edit_summary' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $article, $request) {

            $article->update([
                'title' => $data['title'],
                'content' => $data['content'],
                'last_edited_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->recordRevision($article, $data['content'], $data['edit_summary'] ?? 'Edited the article.', $request, $data['title']);
        });

        return redirect()
            ->route('wiki.articles.show', $article)
            ->with('success', 'Article updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Administrator (Sysop) — protect, delete, restore
    |--------------------------------------------------------------------------
    */

    public function protect(Request $request, Article $article)
    {
        $this->authorizePermission('moderate-content');

        $data = $request->validate([
            'protection_level' => ['required', 'in:none,semi,full'],
        ]);

        $article->update([
            'protection_level' => $data['protection_level'],
            'protected_by' => Auth::id(),
            'protected_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Page protection updated.');
    }

    public function destroy(Article $article)
    {
        $this->authorizePermission('moderate-content');

        $article->update(['deleted_by' => Auth::id()]);
        $article->delete();

        return redirect()
            ->route('wiki.articles.index')
            ->with('success', 'Article deleted.');
    }

    public function restore($articleId)
    {
        $this->authorizePermission('moderate-content');

        $article = Article::withTrashed()->findOrFail($articleId);

        $article->restore();
        $article->update([
            'restored_by' => Auth::id(),
            'restored_at' => now(),
        ]);

        return redirect()
            ->route('wiki.articles.show', $article)
            ->with('success', 'Article restored.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function recordRevision(Article $article, string $content, string $summary, Request $request, ?string $title = null): void
    {
        ArticleRevision::create([
            'article_id' => $article->id,
            'editor_id' => Auth::id(),
            'title' => $title ?? $article->title,
            'content' => $content,
            'edit_summary' => $summary,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $i = 1;

        while (Article::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-".++$i;
        }

        return $slug;
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('wiki', $permission),
            403,
            'You do not have permission to do this.'
        );
    }

    /**
     * Anyone with edit-articles can edit — unless the page is
     * protected, in which case only the Sysop (moderate-content)
     * or above may touch it.
     */
    protected function authorizeEdit(Article $article): void
    {
        $user = Auth::user();

        abort_unless($user->hasModulePermission('wiki', 'edit-articles'), 403, 'You do not have permission to edit articles.');

        if ($article->isFullyProtected()) {
            abort_unless($user->hasModulePermission('wiki', 'moderate-content'), 403, 'This page is fully protected. Only an Administrator can edit it.');
        }
    }
}
