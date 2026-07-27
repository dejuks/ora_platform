<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\EbookSetting;
use App\Models\User;
use App\Notifications\AppNotification;
use App\Support\NotifiesPermissionHolders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * The ORA eBook Publishing workflow:
 *
 *   Author submits -> Book Editor screens -> (desk reject, or)
 *   Book Editor assigns Peer Reviewers -> Reviewers submit verdicts ->
 *   Book Editor makes the editorial decision (accept / minor revision /
 *   major revision / reject) ->
 *   Author pays (or is granted a waiver for) the Book Processing Charge ->
 *   Finance & Operations Officer grants financial clearance ->
 *   Digital Content Manager converts to PDF/EPUB, assigns ISBN/DOI, and
 *   sends the proof to the Author ->
 *   Author approves the proof (or sends it back with change requests) ->
 *   Digital Content Manager sets access rights and publishes to the
 *   ORA Digital Library.
 *
 * Every action is gated by the actual permission that role carries in
 * this module (seeded in RoleSeeder), not by job title.
 */
class BookController extends Controller
{
    use NotifiesPermissionHolders;

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Book::with(['author', 'editor'])->latest();

        if ($user->hasModulePermission('ebook', 'review-manuscripts') && ! $this->isEditorial($user)) {
            // Plain Peer Reviewer: only books assigned to them.
            $query->whereHas('reviews', fn ($q) => $q->where('reviewer_id', $user->id));
        } elseif (! $this->isEditorial($user) && ! $user->isSuperAdmin()) {
            // Plain Author: only their own submissions.
            $query->where('author_id', $user->id);
        }
        // Book Editor / Digital Content Manager / Finance Officer / Super Admin see everything.

        $books = $query->paginate(15)->withQueryString();

        return view('modules.ebook.books.index', compact('books'));
    }

    public function create()
    {
        return view('modules.ebook.books.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'manuscript_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $data['manuscript_file'] = $request->file('manuscript_file')->store('books/manuscripts', 'public');
        $data['author_id'] = Auth::id();
        $data['status'] = 'submitted';
        $data['submitted_at'] = now();
        $data['created_by'] = Auth::id();

        $book = Book::create($data);

        $this->notifyPermissionHolders('ebook', 'screen-manuscripts', new AppNotification(
            title: 'New book manuscript submitted',
            message: "\"{$book->title}\" was submitted by {$book->author->full_name} and needs screening.",
            url: route('ebook.books.show', $book),
            icon: 'bi-file-earmark-text',
            type: 'info',
        ));

        return redirect()
            ->route('ebook.books.show', $book)
            ->with('success', 'Manuscript submitted successfully.');
    }

    public function show(Book $book)
    {
        $this->authorizeView($book);

        $book->load(['author', 'editor', 'decidedBy', 'clearedBy', 'producedBy', 'reviews.reviewer']);

        $reviewers = $this->isEditorial(Auth::user())
            ? User::whereHas('moduleRoles', function ($q) {
                $q->whereHas('module', fn ($m) => $m->where('code', 'ebook'))
                    ->where('slug', 'peer-reviewer');
            })->get()
            : collect();

        return view('modules.ebook.books.show', compact('book', 'reviewers'));
    }

    /**
     * Book Editor: initial screening. Either desk-reject or advance
     * the book to peer review.
     */
    public function screen(Request $request, Book $book)
    {
        $this->authorizePermission('screen-manuscripts');

        $data = $request->validate([
            'decision' => ['required', 'in:advance,desk_reject'],
            'notes' => ['nullable', 'string'],
        ]);

        $book->update([
            'status' => $data['decision'] === 'advance' ? 'screening' : 'desk_rejected',
            'editor_id' => Auth::id(),
            'editor_decision_notes' => $data['notes'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        if ($data['decision'] === 'advance') {
            $book->author?->notify(new AppNotification(
                title: 'Manuscript passed screening',
                message: "\"{$book->title}\" passed editorial screening and will proceed to peer review.",
                url: route('ebook.books.show', $book),
                icon: 'bi-arrow-right-circle',
                type: 'success',
            ));

            $this->notifyPermissionHolders('ebook', 'assign-peer-reviewers', new AppNotification(
                title: 'Manuscript needs reviewers',
                message: "\"{$book->title}\" passed screening and needs peer reviewers assigned.",
                url: route('ebook.books.show', $book),
                icon: 'bi-people',
                type: 'info',
            ), excludeUserId: Auth::id());
        } else {
            $book->author?->notify(new AppNotification(
                title: 'Manuscript desk-rejected',
                message: "\"{$book->title}\" was desk-rejected at editorial screening."
                    .($data['notes'] ?? null ? ' Notes: "'.$data['notes'].'"' : ''),
                url: route('ebook.books.show', $book),
                icon: 'bi-x-circle',
                type: 'danger',
            ));
        }

        return back()->with('success', 'Screening recorded.');
    }

    /**
     * Book Editor: assign one or more peer reviewers.
     */
    public function assignReviewers(Request $request, Book $book)
    {
        $this->authorizePermission('assign-peer-reviewers');

        $data = $request->validate([
            'reviewers' => ['required', 'array', 'min:1'],
            'reviewers.*' => ['exists:users,id'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);

        DB::transaction(function () use ($data, $book) {

            $reviewers = User::whereIn('id', $data['reviewers'])->get();

            foreach ($data['reviewers'] as $reviewerId) {

                BookReview::updateOrCreate(
                    ['book_id' => $book->id, 'reviewer_id' => $reviewerId],
                    [
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'due_date' => $data['due_date'] ?? null,
                    ]
                );
            }

            $book->update([
                'status' => 'under_review',
                'updated_by' => Auth::id(),
            ]);

            $reviewers->each(fn ($reviewer) => $reviewer->notify(new AppNotification(
                title: 'New review assignment',
                message: "You've been assigned to review \"{$book->title}\""
                    .(($data['due_date'] ?? null) ? ", due {$data['due_date']}." : '.'),
                url: route('ebook.books.show', $book),
                icon: 'bi-clipboard-check',
                type: 'info',
            )));

            $book->author?->notify(new AppNotification(
                title: 'Reviewers assigned',
                message: "Reviewers have been assigned to \"{$book->title}\" — it's now under peer review.",
                url: route('ebook.books.show', $book),
                icon: 'bi-people',
                type: 'info',
            ));
        });

        return back()->with('success', 'Peer reviewer(s) assigned.');
    }

    /**
     * Peer Reviewer: submit their verdict for their own assignment only.
     */
    public function submitReview(Request $request, Book $book, BookReview $review)
    {
        abort_unless($review->book_id === $book->id, 404);
        abort_unless($review->reviewer_id === Auth::id(), 403, 'This review is not assigned to you.');

        $data = $request->validate([
            'recommendation' => ['required', 'in:accept,minor_revision,major_revision,reject'],
            'comments_to_author' => ['nullable', 'string'],
            'comments_to_editor' => ['nullable', 'string'],
        ]);

        $data['status'] = 'submitted';
        $data['submitted_at'] = now();

        $review->update($data);

        $this->notifyPermissionHolders('ebook', 'make-editorial-decision', new AppNotification(
            title: 'Review submitted',
            message: "{$review->reviewer->full_name} submitted a review for \"{$book->title}\" ({$data['recommendation']}).",
            url: route('ebook.books.show', $book),
            icon: 'bi-clipboard-check',
            type: 'info',
        ), excludeUserId: Auth::id());

        return back()->with('success', 'Review submitted. Thank you.');
    }

    /**
     * Book Editor: the editorial call — accept, request a minor
     * revision, request a major revision, or reject. Accepting sets
     * the Book Processing Charge and moves the book into the
     * financial-clearance stage. Every other outcome (minor revision,
     * major revision, or reject) pauses the workflow until the author
     * revises and resubmits — see Book::REVISABLE_STATUSES.
     */
    public function decide(Request $request, Book $book)
    {
        $this->authorizePermission('make-editorial-decision');

        $data = $request->validate([
            'decision' => ['required', 'in:accepted,rejected,minor_revision,major_revision'],
            'notes' => ['nullable', 'string'],
        ]);

        $book->update([
            'status' => $data['decision'] === 'accepted' ? 'financial_clearance' : $data['decision'],
            'editor_decision_notes' => $data['notes'] ?? $book->editor_decision_notes,
            'decided_by' => Auth::id(),
            'decided_at' => now(),
            'updated_by' => Auth::id(),
            'processing_fee' => $data['decision'] === 'accepted'
                ? EbookSetting::current()->processing_fee
                : $book->processing_fee,
        ]);

        $decisionNotifications = [
            'accepted' => [
                'title' => 'Manuscript accepted',
                'message' => "Congratulations! \"{$book->title}\" has been accepted for publication. "
                    .'A Book Processing Charge must be paid (or a waiver granted) before it moves to production.',
                'icon' => 'bi-check-circle',
                'type' => 'success',
            ],
            'rejected' => [
                'title' => 'Manuscript rejected',
                'message' => "\"{$book->title}\" was not accepted for publication.",
                'icon' => 'bi-x-circle',
                'type' => 'danger',
            ],
            'minor_revision' => [
                'title' => 'Minor revisions requested',
                'message' => "The Book Editor has requested minor revisions to \"{$book->title}\".",
                'icon' => 'bi-pencil-square',
                'type' => 'warning',
            ],
            'major_revision' => [
                'title' => 'Major revisions requested',
                'message' => "The Book Editor has requested major revisions to \"{$book->title}\".",
                'icon' => 'bi-pencil-square',
                'type' => 'warning',
            ],
        ][$data['decision']];

        $book->author?->notify(new AppNotification(
            title: $decisionNotifications['title'],
            message: $decisionNotifications['message'],
            url: $data['decision'] === 'accepted'
                ? route('ebook.books.pay', $book)
                : route('ebook.books.show', $book),
            icon: $decisionNotifications['icon'],
            type: $decisionNotifications['type'],
        ));

        return back()->with('success', 'Decision recorded.');
    }

    /**
     * Author: edit form for their own book, only while it's sitting
     * at a stage that allows it (see Book::isEditable()).
     */
    public function edit(Book $book)
    {
        $this->authorizeAuthorEdit($book);

        return view('modules.ebook.books.edit', compact('book'));
    }

    /**
     * Author: save edits and push the book back into the workflow.
     * This is the fix for the workflow "pausing" forever whenever a
     * book is desk-rejected, or the Book Editor's editorial decision
     * is a minor revision, a major revision, or a final reject —
     * every outcome except an outright accept requires the author to
     * revise the content (and optionally re-upload the file) and
     * resubmit, and it gets routed to the right point in the
     * pipeline automatically (see Book::nextStatusAfterResubmission()).
     */
    public function update(Request $request, Book $book)
    {
        $this->authorizeAuthorEdit($book);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'manuscript_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        if ($request->hasFile('manuscript_file')) {
            if ($book->manuscript_file) {
                Storage::disk('public')->delete($book->manuscript_file);
            }

            $data['manuscript_file'] = $request->file('manuscript_file')->store('books/manuscripts', 'public');
        }

        DB::transaction(function () use ($book, $data) {
            $wasDeskRejectedOrRejected = in_array($book->status, ['desk_rejected', 'rejected']);
            $wasRevisionRequested = in_array($book->status, ['minor_revision', 'major_revision']);

            $newStatus = $book->nextStatusAfterResubmission();

            $book->update([
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'keywords' => $data['keywords'] ?? null,
                'manuscript_file' => $data['manuscript_file'] ?? $book->manuscript_file,
                'status' => $newStatus,
                'submitted_at' => now(),
                // A resubmission opens a fresh decision cycle, so the
                // previous editorial verdict no longer applies.
                'decided_by' => null,
                'decided_at' => null,
                'editor_decision_notes' => null,
                'updated_by' => Auth::id(),
            ]);

            if ($wasRevisionRequested) {
                // Same reviewers, fresh round on the revised
                // manuscript — clear their prior verdicts (minor or
                // major, accept or reject) so the revised book shows
                // up as pending on their dashboard again.
                $book->reviews()->whereIn('status', ['assigned', 'submitted'])->get()->each(function ($review) use ($book) {
                    $review->update([
                        'status' => 'assigned',
                        'recommendation' => null,
                        'comments_to_author' => null,
                        'comments_to_editor' => null,
                        'submitted_at' => null,
                    ]);

                    $review->reviewer?->notify(new AppNotification(
                        title: 'Revised manuscript ready for review',
                        message: "The author revised \"{$book->title}\" — your review is needed again.",
                        url: route('ebook.books.show', $book),
                        icon: 'bi-clipboard-check',
                        type: 'info',
                    ));
                });
            }

            if ($wasDeskRejectedOrRejected) {
                // Re-enters screening cold: clear the prior Book
                // Editor assignment so it lands back in the general
                // screening queue rather than looking "already handled".
                $book->editor_id = null;
                $book->save();

                $this->notifyPermissionHolders('ebook', 'screen-manuscripts', new AppNotification(
                    title: 'Manuscript resubmitted',
                    message: "\"{$book->title}\" was revised and resubmitted — it needs screening again.",
                    url: route('ebook.books.show', $book),
                    icon: 'bi-file-earmark-text',
                    type: 'info',
                ));
            }
        });

        return redirect()
            ->route('ebook.books.show', $book)
            ->with('success', 'Manuscript revised and resubmitted.');
    }

    protected function authorizeAuthorEdit(Book $book): void
    {
        // Matches this controller's existing requestWaiver() convention:
        // author actions on their own book are gated by ownership, not
        // an extra permission (the ebook-author role has no dedicated
        // "upload-revision" permission the way the Journal one does).
        abort_unless($book->author_id === Auth::id(), 403, 'Only the author can edit this manuscript.');

        abort_unless(
            $book->isEditable(),
            422,
            'This manuscript cannot be edited at its current stage.'
        );
    }

    /**
     * Author: request a fee waiver instead of paying the BPC. Left
     * for the Finance & Operations Officer to approve or decline.
     */
    public function requestWaiver(Request $request, Book $book)
    {
        abort_unless($book->author_id === Auth::id(), 403);
        abort_unless($book->status === 'financial_clearance', 422, 'This book is not awaiting financial clearance.');

        $data = $request->validate([
            'waiver_reason' => ['required', 'string', 'max:1000'],
        ]);

        $book->update([
            'waiver_requested' => true,
            'waiver_reason' => $data['waiver_reason'],
            'updated_by' => Auth::id(),
        ]);

        $this->notifyPermissionHolders('ebook', 'manage-payments', new AppNotification(
            title: 'Fee waiver requested',
            message: "{$book->author->full_name} requested a Book Processing Charge waiver for \"{$book->title}\".",
            url: route('ebook.books.show', $book),
            icon: 'bi-cash-coin',
            type: 'info',
        ));

        return back()->with('success', 'Waiver request submitted to the Finance & Operations Officer.');
    }

    /**
     * Finance & Operations Officer: approve or decline a waiver
     * request, or grant clearance once payment has landed.
     */
    public function clear(Request $request, Book $book)
    {
        $this->authorizePermission('manage-payments');

        abort_unless($book->status === 'financial_clearance', 422, 'This book is not awaiting financial clearance.');

        $data = $request->validate([
            'action' => ['required', 'in:approve_waiver,decline_waiver,grant_clearance'],
        ]);

        if ($data['action'] === 'approve_waiver') {
            abort_unless($book->waiver_requested, 422, 'No waiver has been requested for this book.');

            $book->update([
                'payment_status' => 'waived',
                'fee_paid_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            $book->author?->notify(new AppNotification(
                title: 'Fee waiver approved',
                message: "Your Book Processing Charge waiver for \"{$book->title}\" was approved.",
                url: route('ebook.books.show', $book),
                icon: 'bi-check-circle',
                type: 'success',
            ));

            return back()->with('success', 'Fee waiver approved.');
        }

        if ($data['action'] === 'decline_waiver') {
            $book->update([
                'waiver_requested' => false,
                'updated_by' => Auth::id(),
            ]);

            $book->author?->notify(new AppNotification(
                title: 'Fee waiver declined',
                message: "Your Book Processing Charge waiver request for \"{$book->title}\" was declined. Payment is required.",
                url: route('ebook.books.pay', $book),
                icon: 'bi-x-circle',
                type: 'danger',
            ));

            return back()->with('success', 'Waiver request declined. The author must pay the processing fee.');
        }

        // grant_clearance
        abort_unless($book->isFeeSettled(), 422, 'The Book Processing Charge has not been paid or waived yet.');

        $book->update([
            'status' => 'in_production',
            'cleared_by' => Auth::id(),
            'cleared_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        $book->author?->notify(new AppNotification(
            title: 'Financial clearance granted',
            message: "\"{$book->title}\" has cleared financial review and is now in digital production.",
            url: route('ebook.books.show', $book),
            icon: 'bi-check-circle',
            type: 'success',
        ));

        $this->notifyPermissionHolders('ebook', 'convert-and-publish-ebook', new AppNotification(
            title: 'Manuscript ready for production',
            message: "\"{$book->title}\" cleared financial review and is ready for digital production.",
            url: route('ebook.books.show', $book),
            icon: 'bi-file-earmark-arrow-up',
            type: 'info',
        ));

        return back()->with('success', 'Financial clearance granted. Sent to Digital Production.');
    }

    /**
     * Digital Content Manager: convert to PDF/EPUB, assign ISBN/DOI,
     * and send the proof to the author for approval before it goes
     * live. Access rights aren't set here — that happens at the
     * final publish() step, after the author has signed off.
     */
    public function uploadProof(Request $request, Book $book)
    {
        $this->authorizePermission('convert-and-publish-ebook');

        abort_unless(
            in_array($book->status, ['in_production']),
            422,
            'Only books cleared for production can have a proof uploaded.'
        );

        $data = $request->validate([
            'isbn' => ['nullable', 'string', 'max:32'],
            'ebook_pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
            'ebook_epub' => ['nullable', 'file', 'mimes:epub', 'max:51200'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $updates = [
            'isbn' => $data['isbn'] ?: $book->isbn,
            'doi' => $book->doi ?: '10.0000/ora.ebook.'.str_pad((string) $book->id, 6, '0', STR_PAD_LEFT),
            'ebook_pdf' => $request->file('ebook_pdf')->store('books/ebooks', 'public'),
            'produced_by' => Auth::id(),
            'status' => 'proof_review',
            'proof_submitted_at' => now(),
            'proof_change_notes' => null,
            'updated_by' => Auth::id(),
        ];

        if ($request->hasFile('ebook_epub')) {
            $updates['ebook_epub'] = $request->file('ebook_epub')->store('books/ebooks', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $updates['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $book->update($updates);

        $book->author?->notify(new AppNotification(
            title: 'Your proof is ready for review',
            message: "The final proof for \"{$book->title}\" is ready. Please review and approve it before publication.",
            url: route('ebook.books.show', $book),
            icon: 'bi-file-earmark-check',
            type: 'info',
        ));

        return back()->with('success', 'Proof uploaded and sent to the author for approval.');
    }

    /**
     * Author: approve the final proof. This is the checkpoint
     * required before anything is actually made public — matches
     * "Approve final proof before publication" in the Author's
     * responsibilities.
     */
    public function approveProof(Request $request, Book $book)
    {
        abort_unless($book->author_id === Auth::id(), 403, 'Only the corresponding author can approve the proof.');
        abort_unless(Auth::user()->hasModulePermission('ebook', 'approve-final-proof'), 403, 'You do not have permission to do this.');
        abort_unless($book->status === 'proof_review', 422, 'This book has no proof awaiting your approval.');

        $book->update([
            'status' => 'ready_to_publish',
            'proof_approved_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        $this->notifyPermissionHolders('ebook', 'convert-and-publish-ebook', new AppNotification(
            title: 'Proof approved',
            message: "{$book->author->full_name} approved the proof for \"{$book->title}\" — it's ready to publish.",
            url: route('ebook.books.show', $book),
            icon: 'bi-check-circle',
            type: 'success',
        ));

        return back()->with('success', 'Proof approved. The Digital Content Manager can now publish it.');
    }

    /**
     * Author: send the proof back with change requests instead of
     * approving it. Returns the book to 'in_production' for the
     * Digital Content Manager to fix and re-upload.
     */
    public function requestProofChanges(Request $request, Book $book)
    {
        abort_unless($book->author_id === Auth::id(), 403, 'Only the corresponding author can request changes.');
        abort_unless(Auth::user()->hasModulePermission('ebook', 'approve-final-proof'), 403, 'You do not have permission to do this.');
        abort_unless($book->status === 'proof_review', 422, 'This book has no proof awaiting your review.');

        $data = $request->validate([
            'proof_change_notes' => ['required', 'string', 'max:2000'],
        ]);

        $book->update([
            'status' => 'in_production',
            'proof_change_notes' => $data['proof_change_notes'],
            'updated_by' => Auth::id(),
        ]);

        $this->notifyPermissionHolders('ebook', 'convert-and-publish-ebook', new AppNotification(
            title: 'Author requested proof changes',
            message: "{$book->author->full_name} requested changes to the proof for \"{$book->title}\": \"{$data['proof_change_notes']}\"",
            url: route('ebook.books.show', $book),
            icon: 'bi-exclamation-triangle',
            type: 'warning',
        ));

        return back()->with('success', 'Change request sent to the Digital Content Manager.');
    }

    /**
     * Digital Content Manager: set access rights and actually go
     * live — the last step, only reachable once the author has
     * approved the proof.
     */
    public function publish(Request $request, Book $book)
    {
        $this->authorizePermission('convert-and-publish-ebook');

        abort_unless($book->status === 'ready_to_publish', 422, 'Only books with an author-approved proof can be published.');

        $data = $request->validate([
            'access_type' => ['required', 'in:open_access,restricted,embargoed'],
            'embargo_until' => ['nullable', 'required_if:access_type,embargoed', 'date', 'after:today'],
        ]);

        $book->update([
            'access_type' => $data['access_type'],
            'embargo_until' => $data['access_type'] === 'embargoed' ? $data['embargo_until'] : null,
            'status' => 'published',
            'published_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        $book->author?->notify(new AppNotification(
            title: 'eBook published',
            message: "\"{$book->title}\" has been published to the ORA Digital Library. DOI: {$book->doi}",
            url: route('ebook.public.show', $book),
            icon: 'bi-journal-check',
            type: 'success',
        ));

        return back()->with('success', 'eBook published to the ORA Digital Library.');
    }

    /**
     * Digital Content Manager: change access rights on an already
     * published title (e.g. lift an embargo early, switch to
     * restricted) without re-running the whole production step.
     */
    public function updateAccess(Request $request, Book $book)
    {
        $this->authorizePermission('manage-ebook-access');

        abort_unless($book->status === 'published', 422, 'Only published books have access rights to manage.');

        $data = $request->validate([
            'access_type' => ['required', 'in:open_access,restricted,embargoed'],
            'embargo_until' => ['nullable', 'required_if:access_type,embargoed', 'date'],
        ]);

        $book->update([
            'access_type' => $data['access_type'],
            'embargo_until' => $data['access_type'] === 'embargoed' ? $data['embargo_until'] : null,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Access rights updated.');
    }

    /**
     * Reader: download a published, currently-accessible eBook.
     * Restricted titles require login; embargoed titles are blocked
     * until the embargo date passes; open access is unconditional.
     */
    public function download(Book $book)
    {
        abort_unless($book->isReadableNow(), 404);

        if ($book->access_type === 'restricted') {
            abort_unless(Auth::check(), 401, 'Please sign in to download this restricted title.');
        }

        abort_unless($book->ebook_pdf, 404, 'No file available for this title yet.');

        $book->increment('downloads_count');

        return Storage::disk('public')->download($book->ebook_pdf, $book->title.'.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization helpers
    |--------------------------------------------------------------------------
    */

    protected function isEditorial(User $user): bool
    {
        return $user->isSuperAdmin()
            || $user->hasModulePermission('ebook', 'make-editorial-decision')
            || $user->hasModulePermission('ebook', 'screen-manuscripts')
            || $user->hasModulePermission('ebook', 'convert-and-publish-ebook')
            || $user->hasModulePermission('ebook', 'manage-payments');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('ebook', $permission),
            403,
            'You do not have permission to do this.'
        );
    }

    protected function authorizeView(Book $book): void
    {
        $user = Auth::user();

        if ($user->isSuperAdmin() || $this->isEditorial($user)) {
            return;
        }

        if ($book->author_id === $user->id) {
            return;
        }

        if ($book->reviews()->where('reviewer_id', $user->id)->exists()) {
            return;
        }

        abort(403, 'You do not have access to this book.');
    }
}
