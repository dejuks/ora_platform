<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleDeletionComment;
use App\Models\ArticleDeletionDiscussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Articles for Deletion (AfD): any Registered Editor can nominate an
 * article, the community discusses it (keep / delete / comment), and
 * an Administrator (Sysop) closes the discussion once consensus is
 * reached — deleting the article (soft delete, still restorable) or
 * keeping it.
 */
class DeletionDiscussionController extends Controller
{
    public function index()
    {
        $discussions = ArticleDeletionDiscussion::with(['article', 'openedBy'])
            ->latest()
            ->paginate(15);

        return view('modules.wiki.deletions.index', compact('discussions'));
    }

    public function store(Request $request, Article $article)
    {
        $this->authorizePermission('edit-articles');

        abort_if($article->openDeletionDiscussion()->exists(), 422, 'This article already has an open deletion discussion.');

        $data = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $discussion = ArticleDeletionDiscussion::create([
            'article_id' => $article->id,
            'opened_by' => Auth::id(),
            'reason' => $data['reason'],
            'status' => 'open',
        ]);

        return redirect()
            ->route('wiki.deletions.show', $discussion)
            ->with('success', 'Deletion discussion opened.');
    }

    public function show(ArticleDeletionDiscussion $discussion)
    {
        $discussion->load(['article', 'openedBy', 'closedBy', 'comments.user']);

        return view('modules.wiki.deletions.show', compact('discussion'));
    }

    /**
     * Any module member can weigh in with a stance.
     */
    public function comment(Request $request, ArticleDeletionDiscussion $discussion)
    {
        abort_unless($discussion->isOpen(), 422, 'This discussion is already closed.');

        $data = $request->validate([
            'stance' => ['required', 'in:keep,delete,comment'],
            'comment' => ['required', 'string'],
        ]);

        ArticleDeletionComment::create([
            'discussion_id' => $discussion->id,
            'user_id' => Auth::id(),
            'stance' => $data['stance'],
            'comment' => $data['comment'],
        ]);

        return back()->with('success', 'Comment added.');
    }

    /**
     * Administrator (Sysop): close the discussion, either deleting
     * the article or keeping it.
     */
    public function close(Request $request, ArticleDeletionDiscussion $discussion)
    {
        $this->authorizePermission('moderate-content');

        abort_unless($discussion->isOpen(), 422, 'This discussion is already closed.');

        $data = $request->validate([
            'outcome' => ['required', 'in:keep,delete'],
            'closing_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $discussion) {

            $discussion->update([
                'status' => $data['outcome'] === 'delete' ? 'closed_delete' : 'closed_keep',
                'closed_by' => Auth::id(),
                'closing_notes' => $data['closing_notes'] ?? null,
                'closed_at' => now(),
            ]);

            if ($data['outcome'] === 'delete') {
                $article = $discussion->article;
                $article->update(['deleted_by' => Auth::id()]);
                $article->delete();
            }
        });

        return redirect()
            ->route('wiki.deletions.show', $discussion)
            ->with('success', 'Discussion closed.');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('wiki', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
