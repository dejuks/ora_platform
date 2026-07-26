<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleEditRequest;
use App\Notifications\AppNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Owner-approval editing workflow:
 * - The article's owner and any Sysop/Bureaucrat can always edit.
 * - Anyone else must request access; the owner (or a Sysop/
 *   Bureaucrat, standing in for the owner) approves or rejects.
 * - An approval is good for exactly one save (see
 *   ArticleController::consumeEditRequestIfApplicable) — after
 *   that, the person has to request again.
 */
class ArticleEditRequestController extends Controller
{
    /**
     * Every pending request waiting on a decision from the current
     * user — either because they own the article, or because
     * they're a Sysop/Bureaucrat who can decide on any owner's
     * behalf.
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasModulePermission('wiki', 'moderate-content');

        $requests = ArticleEditRequest::with(['article', 'requester'])
            ->pending()
            ->when(! $isAdmin, fn ($q) => $q->whereHas('article', fn ($a) => $a->where('author_id', $user->id)))
            ->latest()
            ->paginate(20);

        return view('modules.wiki.edit-requests.index', compact('requests'));
    }

    public function store(Request $request, Article $article)
    {
        $user = Auth::user();

        abort_unless($user->hasModulePermission('wiki', 'edit-articles'), 403, 'You do not have permission to edit articles.');

        if ($article->author_id === $user->id) {
            return back()->with('info', 'You already own this article — you can edit it directly.');
        }

        if ($user->hasModulePermission('wiki', 'moderate-content')) {
            return back()->with('info', 'As an Administrator, you can already edit this article directly.');
        }

        if ($article->pendingEditRequestFor($user)) {
            return back()->with('error', 'You already have a pending request for this article.');
        }

        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $editRequest = ArticleEditRequest::create([
            'article_id' => $article->id,
            'requester_id' => $user->id,
            'message' => $data['message'] ?? null,
            'status' => 'pending',
        ]);

        ActivityLogger::log(
            'wiki.edit_request.sent',
            "Requested edit access to \"{$article->title}\"",
            $article
        );

        $article->author?->notify(new AppNotification(
            title: 'New edit request',
            message: "{$user->full_name} wants to edit \"{$article->title}\".".($data['message'] ?? '' ? ' "'.$data['message'].'"' : ''),
            url: route('wiki.articles.show', $article),
            icon: 'bi-envelope',
            type: 'info',
        ));

        return back()->with('success', 'Edit request sent to the article owner.');
    }

    public function approve(Article $article, ArticleEditRequest $editRequest)
    {
        $this->authorizeDecision($article, $editRequest);

        $editRequest->update([
            'status' => 'approved',
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        ActivityLogger::log(
            'wiki.edit_request.approved',
            "Approved edit request from {$editRequest->requester->full_name} on \"{$article->title}\"",
            $article
        );

        $editRequest->requester?->notify(new AppNotification(
            title: 'Edit request approved',
            message: "You can now make one edit to \"{$article->title}\".",
            url: route('wiki.articles.edit', $article),
            icon: 'bi-check-circle',
            type: 'success',
        ));

        return back()->with('success', "Approved. {$editRequest->requester->full_name} can make one edit to this article.");
    }

    public function reject(Article $article, ArticleEditRequest $editRequest)
    {
        $this->authorizeDecision($article, $editRequest);

        $editRequest->update([
            'status' => 'rejected',
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        ActivityLogger::log(
            'wiki.edit_request.rejected',
            "Rejected edit request from {$editRequest->requester->full_name} on \"{$article->title}\"",
            $article
        );

        $editRequest->requester?->notify(new AppNotification(
            title: 'Edit request rejected',
            message: "Your request to edit \"{$article->title}\" was not approved.",
            url: route('wiki.articles.show', $article),
            icon: 'bi-x-circle',
            type: 'danger',
        ));

        return back()->with('success', 'Request rejected.');
    }

    /**
     * Only the article's owner, or a Sysop/Bureaucrat standing in
     * for them, may approve/reject a request on that article — and
     * only while it's still pending.
     */
    protected function authorizeDecision(Article $article, ArticleEditRequest $editRequest): void
    {
        abort_unless($editRequest->article_id === $article->id, 404);

        $user = Auth::user();
        $isOwner = $article->author_id === $user->id;
        $isAdmin = $user->hasModulePermission('wiki', 'moderate-content');

        abort_unless($isOwner || $isAdmin, 403, 'Only the article owner or an Administrator can decide this request.');

        abort_unless($editRequest->status === 'pending', 409, 'This request has already been decided.');
    }
}
