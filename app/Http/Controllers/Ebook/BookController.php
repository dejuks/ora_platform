<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookReview;
use App\Models\EbookSetting;
use App\Models\User;
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
 *   Digital Content Manager converts to PDF/EPUB, assigns ISBN/DOI,
 *   sets access rights, and publishes to the ORA Digital Library.
 *
 * Every action is gated by the actual permission that role carries in
 * this module (seeded in RoleSeeder), not by job title.
 */
class BookController extends Controller
{
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
        $categories = BookCategory::active()->ordered()->get();

        return view('modules.ebook.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:ebook_categories,id'],
            'manuscript_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $data['manuscript_file'] = $request->file('manuscript_file')->store('books/manuscripts', 'public');
        $data['author_id'] = Auth::id();
        $data['status'] = 'submitted';
        $data['submitted_at'] = now();
        $data['created_by'] = Auth::id();

        $book = Book::create($data);

        return redirect()
            ->route('ebook.books.show', $book)
            ->with('success', 'Manuscript submitted successfully.');
    }

    public function show(Book $book)
    {
        $this->authorizeView($book);

        $book->load(['author', 'category', 'editor', 'decidedBy', 'clearedBy', 'producedBy', 'reviews.reviewer']);

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

        return back()->with('success', 'Decision recorded.');
    }

    /**
     * Author: edit form for their own book, only while it's sitting
     * at a stage that allows it (see Book::isEditable()).
     */
    public function edit(Book $book)
    {
        $this->authorizeAuthorEdit($book);

        $categories = BookCategory::active()->ordered()->get();

        return view('modules.ebook.books.edit', compact('book', 'categories'));
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
            'category_id' => ['nullable', 'exists:ebook_categories,id'],
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
                'category_id' => $data['category_id'] ?? $book->category_id,
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
                $book->reviews()->whereIn('status', ['assigned', 'submitted'])->get()->each(function ($review) {
                    $review->update([
                        'status' => 'assigned',
                        'recommendation' => null,
                        'comments_to_author' => null,
                        'comments_to_editor' => null,
                        'submitted_at' => null,
                    ]);
                });
            }

            if ($wasDeskRejectedOrRejected) {
                // Re-enters screening cold: clear the prior Book
                // Editor assignment so it lands back in the general
                // screening queue rather than looking "already handled".
                $book->editor_id = null;
                $book->save();
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

            return back()->with('success', 'Fee waiver approved.');
        }

        if ($data['action'] === 'decline_waiver') {
            $book->update([
                'waiver_requested' => false,
                'updated_by' => Auth::id(),
            ]);

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

        return back()->with('success', 'Financial clearance granted. Sent to Digital Production.');
    }

    /**
     * Digital Content Manager: convert to PDF/EPUB, assign ISBN/DOI,
     * set access rights, and publish to the ORA Digital Library.
     */
    public function publish(Request $request, Book $book)
    {
        $this->authorizePermission('convert-and-publish-ebook');

        abort_unless($book->status === 'in_production', 422, 'Only books cleared for production can be published.');

        $data = $request->validate([
            'isbn' => ['nullable', 'string', 'max:32'],
            'ebook_pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
            'ebook_epub' => ['nullable', 'file', 'mimes:epub', 'max:51200'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'access_type' => ['required', 'in:open_access,restricted,embargoed'],
            'embargo_until' => ['nullable', 'required_if:access_type,embargoed', 'date', 'after:today'],
        ]);

        $updates = [
            'isbn' => $data['isbn'] ?: $book->isbn,
            'doi' => $book->doi ?: '10.0000/ora.ebook.'.str_pad((string) $book->id, 6, '0', STR_PAD_LEFT),
            'ebook_pdf' => $request->file('ebook_pdf')->store('books/ebooks', 'public'),
            'access_type' => $data['access_type'],
            'embargo_until' => $data['access_type'] === 'embargoed' ? $data['embargo_until'] : null,
            'produced_by' => Auth::id(),
            'status' => 'published',
            'published_at' => now(),
            'updated_by' => Auth::id(),
        ];

        if ($request->hasFile('ebook_epub')) {
            $updates['ebook_epub'] = $request->file('ebook_epub')->store('books/ebooks', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $updates['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $book->update($updates);

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
     * until the embargo date passes; open access is unconditional;
     * 'for_sale' titles require a completed purchase (see EbookOrder
     * / Ebook\OrderController) — isReadableNow() excludes 'for_sale'
     * entirely, so that case is handled separately below.
     */
    public function download(Book $book)
    {
        $isForSale = $book->status === 'published' && $book->access_type === 'for_sale';

        abort_unless($book->isReadableNow() || $isForSale, 404);

        if ($book->access_type === 'restricted') {
            abort_unless(Auth::check(), 401, 'Please sign in to download this restricted title.');
        }

        if ($isForSale) {
            abort_unless(Auth::check(), 401, 'Please sign in to download this title.');
            abort_unless(
                $book->isPurchasedBy(Auth::user()) || Auth::user()->isSuperAdmin(),
                403,
                'You need to purchase this title before downloading it.'
            );
        }

        abort_unless($book->ebook_pdf, 404, 'No file available for this title yet.');

        $book->increment('downloads_count');

        if ($isForSale) {
            $book->orders()
                ->where('user_id', Auth::id())
                ->where('status', 'completed')
                ->increment('download_count');
        }

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
