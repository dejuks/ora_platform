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
        $query = Article::with(['author', 'lastEditedBy', 'categories'])
            ->withTrashed()
            ->latest('updated_at');

        if ($search = $request->query('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categorySlug = $request->query('category')) {
            $query->inCategory($categorySlug);
        }

        $articles = $query->paginate(15)->withQueryString();
        $categories = \App\Models\WikiCategory::active()->ordered()->get();

        return view('modules.wiki.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $this->authorizePermission('edit-articles');

        $categories = \App\Models\WikiCategory::active()->ordered()->get();

        return view('modules.wiki.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('edit-articles');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'edit_summary' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:wiki_categories,id'],
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

            $article->categories()->sync($data['category_ids'] ?? []);

            $this->recordRevision($article, $data['content'], $data['edit_summary'] ?? 'Created the article.', $request);

            return $article;
        });

        return redirect()
            ->route('wiki.articles.show', $article)
            ->with('success', 'Article created.');
    }

    public function show(Article $article)
    {
        $article = Article::withTrashed()->with(['author', 'lastEditedBy', 'protectedBy', 'categories'])->findOrFail($article->id);

        $article->load(['revisions' => function ($q) {
            $q->where('is_suppressed', false)->with('editor');
        }]);

        $openDiscussion = $article->openDeletionDiscussion()->with(['openedBy', 'comments.user'])->first();

        $user = Auth::user();
        $isOwner = $article->author_id === $user->id;
        $canModerate = $user->hasModulePermission('wiki', 'moderate-content');
        $myConsumableRequest = $article->consumableEditRequestFor($user);
        $myPendingRequest = $article->pendingEditRequestFor($user);
        $canEditThisArticle = $canModerate
            || (! $article->isFullyProtected() && ($isOwner || $myConsumableRequest !== null));

        $pendingRequestsToDecide = ($isOwner || $canModerate)
            ? $article->editRequests()->pending()->with('requester')->latest()->get()
            : collect();

        return view('modules.wiki.articles.show', compact(
            'article',
            'openDiscussion',
            'isOwner',
            'canEditThisArticle',
            'myConsumableRequest',
            'myPendingRequest',
            'pendingRequestsToDecide'
        ));
    }

    public function edit(Article $article)
    {
        $this->authorizeEdit($article);

        $categories = \App\Models\WikiCategory::active()->ordered()->get();
        $selectedCategoryIds = $article->categories()->pluck('wiki_categories.id')->all();

        return view('modules.wiki.articles.edit', compact('article', 'categories', 'selectedCategoryIds'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorizeEdit($article);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'edit_summary' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:wiki_categories,id'],
        ]);

        DB::transaction(function () use ($data, $article, $request) {

            $article->update([
                'title' => $data['title'],
                'content' => $data['content'],
                'last_edited_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $article->categories()->sync($data['category_ids'] ?? []);

            $this->consumeEditRequestIfApplicable($article, Auth::user());

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
     * Editing model: the article's owner (author) and any
     * Sysop/Bureaucrat (moderate-content) can always edit. Anyone
     * else needs an owner-approved, not-yet-used edit request —
     * see ArticleEditRequestController. Page protection still beats
     * everyone except moderate-content, same as before.
     */
    protected function authorizeEdit(Article $article): void
    {
        $user = Auth::user();

        abort_unless($user->hasModulePermission('wiki', 'edit-articles'), 403, 'You do not have permission to edit articles.');

        if ($user->hasModulePermission('wiki', 'moderate-content')) {
            return;
        }

        if ($article->isFullyProtected()) {
            abort(403, 'This page is fully protected. Only an Administrator can edit it.');
        }

        if ($article->author_id === $user->id) {
            return;
        }

        abort_unless(
            $article->consumableEditRequestFor($user) !== null,
            403,
            'You need the owner\'s approval before editing this article. Request edit access from the article page.'
        );
    }

    /**
     * If this save is happening on someone else's article via an
     * approved request (not ownership, not admin override), spend
     * that one-time approval now so they need to ask again next time.
     */
    protected function consumeEditRequestIfApplicable(Article $article, $user): void
    {
        if ($user->hasModulePermission('wiki', 'moderate-content')) {
            return;
        }

        if ($article->author_id === $user->id) {
            return;
        }

        $article->consumableEditRequestFor($user)?->markUsed();
    }
}
