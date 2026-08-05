<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use App\Models\ManuscriptCoAuthor;
use App\Models\ManuscriptReview;
use App\Models\JournalCategory;
use App\Models\JournalSetting;
use App\Models\User;
use App\Notifications\AppNotification;
use App\Support\NotifiesPermissionHolders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

/**
 * The real Journal Management workflow:
 *
 *   Author submits -> Associate Editor screens -> (desk reject, or)
 *   Associate Editor assigns Reviewers -> Reviewers submit verdicts ->
 *   Associate Editor recommends -> Editor-in-Chief decides ->
 *   Journal Manager / EIC publishes (assigns DOI)
 *
 * Every action below is gated by the actual permission that role
 * carries in this module (seeded in RoleSeeder), not by job title —
 * so if a Super Admin reassigns who holds "Associate Editor" later,
 * these checks keep working with zero code changes.
 */
class ManuscriptController extends Controller
{
    use NotifiesPermissionHolders;

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Manuscript::with(['author', 'associateEditor'])->latest();

        if ($user->hasModulePermission('journal', 'review-manuscripts') && ! $this->isEditorial($user)) {
            // Plain Reviewer: only manuscripts they're assigned to.
            $query->whereHas('reviews', fn ($q) => $q->where('reviewer_id', $user->id));
        } elseif (! $this->isEditorial($user) && ! $user->isSuperAdmin()) {
            // Plain Author: only their own submissions.
            $query->where('author_id', $user->id);
        } else {
            // Journal Manager / EIC / Associate Editor / Super Admin see
            // everything that's actually been pushed into the workflow —
            // but not other people's drafts, which are private to the author.
            $query->where(function ($q) use ($user) {
                $q->where('status', '!=', 'draft')->orWhere('author_id', $user->id);
            });
        }

        $manuscripts = $query->paginate(15)->withQueryString();

        // Double-blind peer review: a plain Reviewer never sees who
        // wrote what they're reviewing. Editorial roles and the
        // author themselves still see full identity everywhere.
        $blindAuthor = $this->isReviewerOnly($user);

        return view('modules.journal.manuscripts.index', compact('manuscripts', 'blindAuthor'));
    }

    public function create()
    {
        $categories = JournalCategory::active()->ordered()->get();

        return view('modules.journal.manuscripts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:journal_categories,id'],
            // A draft can be saved without a file yet; pushing it into
            // the review workflow requires one.
            'manuscript_file' => ['required_if:action,submit', 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'action' => ['required', 'in:draft,submit'],
            // Co-authors: unlimited, and every field but the name is
            // genuinely optional (see ManuscriptCoAuthor migration).
            // A row with no name typed in is silently dropped rather
            // than rejected — that's just an empty row the author
            // added and then didn't fill in.
            'co_authors' => ['nullable', 'array'],
            'co_authors.*.full_name' => ['nullable', 'string', 'max:150'],
            'co_authors.*.email' => ['nullable', 'email', 'max:150'],
            'co_authors.*.affiliation' => ['nullable', 'string', 'max:255'],
            'co_authors.*.orcid' => ['nullable', 'string', 'max:50'],
        ]);

        // CKEditor runs client-side only — this endpoint still accepts
        // raw POST data, so sanitize server-side regardless of what
        // was actually submitted through the editor.
        $data['abstract'] = Purifier::clean($data['abstract'], 'manuscript_abstract');

        $isSubmit = $data['action'] === 'submit';

        if ($request->hasFile('manuscript_file')) {
            $data['manuscript_file'] = $request->file('manuscript_file')->store('manuscripts', 'public');
        } else {
            unset($data['manuscript_file']);
        }

        $data['author_id'] = Auth::id();
        $data['status'] = $isSubmit ? 'submitted' : 'draft';
        $data['submitted_at'] = $isSubmit ? now() : null;
        $data['created_by'] = Auth::id();
        unset($data['action']);

        $coAuthors = $data['co_authors'] ?? [];
        unset($data['co_authors']);

        $manuscript = Manuscript::create($data);

        $this->syncCoAuthors($manuscript, $coAuthors);

        if ($isSubmit) {
            $this->notifyPermissionHolders('journal', 'screen-submissions', new AppNotification(
                title: 'New manuscript submitted',
                message: "\"{$manuscript->title}\" was submitted by {$manuscript->author->full_name} and needs screening.",
                url: route('journal.manuscripts.show', $manuscript),
                icon: 'bi-file-earmark-text',
                type: 'info',
            ));
        }

        $message = $isSubmit
            ? 'Manuscript submitted successfully.'
            : 'Draft saved. Only you can see it until you push it for review.';

        return redirect()
            ->route('journal.manuscripts.show', $manuscript)
            ->with('success', $message);
    }

    public function show(Manuscript $manuscript)
    {
        $this->authorizeView($manuscript);

        $manuscript->load(['author', 'associateEditor', 'decidedBy', 'reviews.reviewer', 'coAuthors']);

        $reviewers = $this->isEditorial(Auth::user())
            ? User::whereHas('moduleRoles', function ($q) {
                $q->whereHas('module', fn ($m) => $m->where('code', 'journal'))
                    ->where('slug', 'reviewer');
            })->get()
            : collect();

        $blindAuthor = $this->isReviewerOnly(Auth::user());

        return view('modules.journal.manuscripts.show', compact('manuscript', 'reviewers', 'blindAuthor'));
    }

    /**
     * Author: edit form for their own manuscript, only while it's
     * sitting at a stage that allows it (see Manuscript::isEditable()).
     */
    public function edit(Manuscript $manuscript)
    {
        $this->authorizeAuthorEdit($manuscript);

        $categories = JournalCategory::active()->ordered()->get();

        return view('modules.journal.manuscripts.edit', compact('manuscript', 'categories'));
    }

    /**
     * Author: save edits and push the manuscript back into the
     * workflow. This is the fix for the workflow "pausing" forever
     * whenever a manuscript is desk-rejected, sent back for revision,
     * or finally rejected — the author can now revise the content
     * (and optionally re-upload the file) and resubmit from any of
     * those stages, and it gets routed to the right point in the
     * pipeline automatically (see Manuscript::nextStatusAfterResubmission()).
     */
    public function update(Request $request, Manuscript $manuscript)
    {
        $this->authorizeAuthorEdit($manuscript);

        $isDraft = $manuscript->status === 'draft';

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:journal_categories,id'],
            'manuscript_file' => [
                $isDraft ? 'required_if:action,submit' : 'nullable',
                'nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240',
            ],
            'action' => [$isDraft ? 'required' : 'nullable', 'in:draft,submit'],
            'co_authors' => ['nullable', 'array'],
            'co_authors.*.full_name' => ['nullable', 'string', 'max:150'],
            'co_authors.*.email' => ['nullable', 'email', 'max:150'],
            'co_authors.*.affiliation' => ['nullable', 'string', 'max:255'],
            'co_authors.*.orcid' => ['nullable', 'string', 'max:50'],
        ]);

        $data['abstract'] = Purifier::clean($data['abstract'], 'manuscript_abstract');

        $coAuthors = $data['co_authors'] ?? [];
        unset($data['co_authors']);

        if ($request->hasFile('manuscript_file')) {
            if ($manuscript->manuscript_file) {
                Storage::disk('public')->delete($manuscript->manuscript_file);
            }

            $data['manuscript_file'] = $request->file('manuscript_file')->store('manuscripts', 'public');
        }

        if ($isDraft) {
            // A draft only ever moves between "still a draft" and
            // "pushed into the review pipeline" — none of the
            // resubmission/reviewer-reset logic below applies, since
            // it has never been through screening or review yet.
            $isSubmit = $data['action'] === 'submit';

            $manuscript->update([
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'keywords' => $data['keywords'] ?? null,
                'category_id' => $data['category_id'] ?? $manuscript->category_id,
                'manuscript_file' => $data['manuscript_file'] ?? $manuscript->manuscript_file,
                'status' => $isSubmit ? 'submitted' : 'draft',
                'submitted_at' => $isSubmit ? now() : null,
                'updated_by' => Auth::id(),
            ]);

            $this->syncCoAuthors($manuscript, $coAuthors);

            if ($isSubmit) {
                $this->notifyPermissionHolders('journal', 'screen-submissions', new AppNotification(
                    title: 'New manuscript submitted',
                    message: "\"{$manuscript->title}\" was submitted by {$manuscript->author->full_name} and needs screening.",
                    url: route('journal.manuscripts.show', $manuscript),
                    icon: 'bi-file-earmark-text',
                    type: 'info',
                ));
            }

            $message = $isSubmit
                ? 'Manuscript submitted successfully.'
                : 'Draft saved. Only you can see it until you push it for review.';

            return redirect()
                ->route('journal.manuscripts.show', $manuscript)
                ->with('success', $message);
        }

        DB::transaction(function () use ($manuscript, $data, $coAuthors) {
            $wasDeskRejectedOrRejected = in_array($manuscript->status, ['desk_rejected', 'rejected']);
            $wasRevisionRequested = $manuscript->status === 'revision_requested';

            $newStatus = $manuscript->nextStatusAfterResubmission();

            $manuscript->update([
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'keywords' => $data['keywords'] ?? null,
                'category_id' => $data['category_id'] ?? $manuscript->category_id,
                'manuscript_file' => $data['manuscript_file'] ?? $manuscript->manuscript_file,
                'status' => $newStatus,
                'submitted_at' => now(),
                // A resubmission opens a fresh decision cycle, so the
                // previous editorial verdict no longer applies.
                'decided_by' => null,
                'decided_at' => null,
                'editor_decision_notes' => null,
                'updated_by' => Auth::id(),
            ]);

            $this->syncCoAuthors($manuscript, $coAuthors);

            if ($wasRevisionRequested) {
                // Same reviewers, fresh round on the revised file —
                // clear their prior verdicts so the revised manuscript
                // shows up as pending on their dashboard again.
                $manuscript->reviews()->whereIn('status', ['assigned', 'submitted'])->get()->each(function ($review) use ($manuscript) {
                    $review->update([
                        'status' => 'assigned',
                        'recommendation' => null,
                        'comments_to_author' => null,
                        'comments_to_editor' => null,
                        'submitted_at' => null,
                    ]);

                    $review->reviewer?->notify(new AppNotification(
                        title: 'Revised manuscript ready for review',
                        message: "The author revised \"{$manuscript->title}\" — your review is needed again.",
                        url: route('journal.manuscripts.show', $manuscript),
                        icon: 'bi-clipboard-check',
                        type: 'info',
                    ));
                });
            }

            if ($wasDeskRejectedOrRejected) {
                // Re-enters screening cold: clear the prior Associate
                // Editor assignment so it lands back in the general
                // screening queue rather than looking "already handled".
                $manuscript->associate_editor_id = null;
                $manuscript->save();

                $this->notifyPermissionHolders('journal', 'screen-submissions', new AppNotification(
                    title: 'Manuscript resubmitted',
                    message: "\"{$manuscript->title}\" was revised and resubmitted — it needs screening again.",
                    url: route('journal.manuscripts.show', $manuscript),
                    icon: 'bi-file-earmark-text',
                    type: 'info',
                ));
            }
        });

        return redirect()
            ->route('journal.manuscripts.show', $manuscript)
            ->with('success', 'Manuscript revised and resubmitted.');
    }

    protected function authorizeAuthorEdit(Manuscript $manuscript): void
    {
        abort_unless($manuscript->author_id === Auth::id(), 403, 'Only the author can edit this manuscript.');

        abort_unless(
            Auth::user()->hasModulePermission('journal', 'upload-revision'),
            403,
            'You do not have permission to do this.'
        );

        abort_unless(
            $manuscript->isEditable(),
            422,
            'This manuscript cannot be edited at its current stage.'
        );
    }

    /**
     * Associate Editor: initial screening. Either desk-reject or
     * advance the manuscript to peer review.
     */
    public function screen(Request $request, Manuscript $manuscript)
    {
        $this->authorizePermission('screen-submissions');

        $data = $request->validate([
            'decision' => ['required', 'in:advance,desk_reject'],
            'notes' => ['nullable', 'string'],
        ]);

        $manuscript->update([
            'status' => $data['decision'] === 'advance' ? 'screening' : 'desk_rejected',
            'associate_editor_id' => Auth::id(),
            'editor_decision_notes' => $data['notes'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        if ($data['decision'] === 'advance') {
            $manuscript->author?->notify(new AppNotification(
                title: 'Manuscript passed screening',
                message: "\"{$manuscript->title}\" passed editorial screening and will proceed to peer review.",
                url: route('journal.manuscripts.show', $manuscript),
                icon: 'bi-arrow-right-circle',
                type: 'success',
            ));

            $this->notifyPermissionHolders('journal', 'assign-reviewers', new AppNotification(
                title: 'Manuscript needs reviewers',
                message: "\"{$manuscript->title}\" passed screening and needs reviewers assigned.",
                url: route('journal.manuscripts.show', $manuscript),
                icon: 'bi-people',
                type: 'info',
            ), excludeUserId: Auth::id());
        } else {
            $manuscript->author?->notify(new AppNotification(
                title: 'Manuscript desk-rejected',
                message: "\"{$manuscript->title}\" was desk-rejected at editorial screening."
                    .($data['notes'] ?? null ? ' Notes: "'.$data['notes'].'"' : ''),
                url: route('journal.manuscripts.show', $manuscript),
                icon: 'bi-x-circle',
                type: 'danger',
            ));
        }

        return back()->with('success', 'Manuscript screening recorded.');
    }

    /**
     * Associate Editor: assign one or more reviewers.
     */
    public function assignReviewers(Request $request, Manuscript $manuscript)
    {
        $this->authorizePermission('assign-reviewers');

        $data = $request->validate([
            'reviewers' => ['required', 'array', 'min:1'],
            'reviewers.*' => ['exists:users,id'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);

        DB::transaction(function () use ($data, $manuscript) {

            $reviewers = User::whereIn('id', $data['reviewers'])->get();

            foreach ($data['reviewers'] as $reviewerId) {

                ManuscriptReview::updateOrCreate(
                    ['manuscript_id' => $manuscript->id, 'reviewer_id' => $reviewerId],
                    [
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'due_date' => $data['due_date'] ?? null,
                    ]
                );
            }

            $manuscript->update([
                'status' => 'under_review',
                'updated_by' => Auth::id(),
            ]);

            $reviewers->each(fn ($reviewer) => $reviewer->notify(new AppNotification(
                title: 'New review assignment',
                message: "You've been assigned to review \"{$manuscript->title}\""
                    .(($data['due_date'] ?? null) ? ", due {$data['due_date']}." : '.'),
                url: route('journal.manuscripts.show', $manuscript),
                icon: 'bi-clipboard-check',
                type: 'info',
            )));

            $manuscript->author?->notify(new AppNotification(
                title: 'Reviewers assigned',
                message: "Reviewers have been assigned to \"{$manuscript->title}\" — it's now under peer review.",
                url: route('journal.manuscripts.show', $manuscript),
                icon: 'bi-people',
                type: 'info',
            ));
        });

        return back()->with('success', 'Reviewer(s) assigned.');
    }

    /**
     * Reviewer: submit their verdict for their own assignment only.
     */
    public function submitReview(Request $request, Manuscript $manuscript, ManuscriptReview $review)
    {
        abort_unless($review->manuscript_id === $manuscript->id, 404);
        abort_unless($review->reviewer_id === Auth::id(), 403, 'This review is not assigned to you.');

        $data = $request->validate([
            'recommendation' => ['required', 'in:accept,minor_revision,major_revision,reject'],
            'comments_to_author' => ['nullable', 'string'],
            'comments_to_editor' => ['nullable', 'string'],
        ]);

        $data['status'] = 'submitted';
        $data['submitted_at'] = now();

        $review->update($data);

        $this->notifyPermissionHolders('journal', 'recommend-decision', new AppNotification(
            title: 'Review submitted',
            message: "{$review->reviewer->full_name} submitted a review for \"{$manuscript->title}\" ({$data['recommendation']}).",
            url: route('journal.manuscripts.show', $manuscript),
            icon: 'bi-clipboard-check',
            type: 'info',
        ), excludeUserId: Auth::id());

        return back()->with('success', 'Review submitted. Thank you.');
    }

    /**
     * Associate Editor: recommend a decision to the Editor-in-Chief
     * once all assigned reviews are in.
     */
    public function recommend(Request $request, Manuscript $manuscript)
    {
        $this->authorizePermission('recommend-decision');

        $data = $request->validate([
            'recommendation_notes' => ['required', 'string'],
        ]);

        $manuscript->update([
            'editor_decision_notes' => $data['recommendation_notes'],
            'updated_by' => Auth::id(),
        ]);

        $this->notifyPermissionHolders('journal', 'make-final-decision', new AppNotification(
            title: 'Recommendation ready for decision',
            message: "{$manuscript->author->full_name}'s manuscript \"{$manuscript->title}\" has a recommendation awaiting your final decision.",
            url: route('journal.manuscripts.show', $manuscript),
            icon: 'bi-flag',
            type: 'info',
        ), excludeUserId: Auth::id());

        return back()->with('success', 'Recommendation sent to the Editor-in-Chief.');
    }

    /**
     * Editor-in-Chief: the final accept / reject / request-revision call.
     */
    public function decide(Request $request, Manuscript $manuscript)
    {
        $this->authorizePermission('make-final-decision');

        $data = $request->validate([
            'decision' => ['required', 'in:accepted,rejected,revision_requested'],
            'notes' => ['nullable', 'string'],
        ]);

        $manuscript->update([
            'status' => $data['decision'],
            'editor_decision_notes' => $data['notes'] ?? $manuscript->editor_decision_notes,
            'decided_by' => Auth::id(),
            'decided_at' => now(),
            'updated_by' => Auth::id(),
            // Accepting a manuscript triggers the Article Processing
            // Charge: the author must settle this before it can be
            // published (see PaymentController). Amount comes from
            // the Journal Manager's payment settings, not a hardcoded
            // config value.
            'publication_fee' => $data['decision'] === 'accepted'
                ? JournalSetting::current()->publication_fee
                : $manuscript->publication_fee,
        ]);

        $decisionNotifications = [
            'accepted' => [
                'title' => 'Manuscript accepted',
                'message' => "Congratulations! \"{$manuscript->title}\" has been accepted for publication. "
                    .'A publication fee must be paid before it can be published.',
                'icon' => 'bi-check-circle',
                'type' => 'success',
            ],
            'rejected' => [
                'title' => 'Manuscript rejected',
                'message' => "\"{$manuscript->title}\" was not accepted for publication.",
                'icon' => 'bi-x-circle',
                'type' => 'danger',
            ],
            'revision_requested' => [
                'title' => 'Revisions requested',
                'message' => "The Editor-in-Chief has requested revisions to \"{$manuscript->title}\".",
                'icon' => 'bi-pencil-square',
                'type' => 'warning',
            ],
        ][$data['decision']];

        $manuscript->author?->notify(new AppNotification(
            title: $decisionNotifications['title'],
            message: $decisionNotifications['message'],
            url: $data['decision'] === 'accepted'
                ? route('journal.manuscripts.pay', $manuscript)
                : route('journal.manuscripts.show', $manuscript),
            icon: $decisionNotifications['icon'],
            type: $decisionNotifications['type'],
        ));

        return back()->with('success', 'Decision recorded.');
    }

    /**
     * Journal Manager: once the Article Processing Charge is settled,
     * send the final, fully-typeset publication proof (the actual
     * document that will go live) to the corresponding author for
     * their review — before it's published, not after.
     */
    public function sendProof(Request $request, Manuscript $manuscript)
    {
        $this->authorizePermission('manage-workflow');

        abort_unless($manuscript->status === 'accepted', 422, 'Only accepted manuscripts have a publication proof to send.');

        abort_unless(
            $manuscript->isFeeSettled(),
            422,
            'The publication fee has not been paid yet. The author must complete payment before a proof can be sent.'
        );

        $data = $request->validate([
            'proof_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'proof_message' => ['nullable', 'string'],
        ]);

        if ($manuscript->proof_file) {
            Storage::disk('public')->delete($manuscript->proof_file);
        }

        $manuscript->update([
            'proof_file' => $request->file('proof_file')->store('manuscript-proofs', 'public'),
            'proof_message' => $data['proof_message'] ?? null,
            'proof_status' => 'sent',
            'proof_feedback' => null,
            'proof_sent_by' => Auth::id(),
            'proof_sent_at' => now(),
            'proof_responded_at' => null,
            'updated_by' => Auth::id(),
        ]);

        $manuscript->author?->notify(new AppNotification(
            title: 'Publication proof ready for your review',
            message: "The final publication proof for \"{$manuscript->title}\" is ready. "
                .'Please review the full document and approve it, or leave comments if changes are needed.',
            url: route('journal.manuscripts.show', $manuscript),
            icon: 'bi-file-earmark-check',
            type: 'info',
        ));

        return back()->with('success', 'Publication proof sent to the author for approval.');
    }

    /**
     * Author: review the publication proof and either approve it
     * (unblocking publish()) or send comments back to the Journal
     * Manager, who revises and re-sends via sendProof() above.
     */
    public function respondToProof(Request $request, Manuscript $manuscript)
    {
        abort_unless($manuscript->author_id === Auth::id(), 403, 'Only the corresponding author can respond to the publication proof.');

        abort_unless($manuscript->isProofAwaitingAuthor(), 422, 'There is no publication proof currently awaiting your review.');

        $data = $request->validate([
            'decision' => ['required', 'in:approved,changes_requested'],
            'feedback' => ['required_if:decision,changes_requested', 'nullable', 'string'],
        ]);

        $manuscript->update([
            'proof_status' => $data['decision'],
            'proof_feedback' => $data['feedback'] ?? null,
            'proof_responded_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        $notification = $data['decision'] === 'approved'
            ? [
                'title' => 'Publication proof approved',
                'message' => "The author approved the publication proof for \"{$manuscript->title}\". It's ready to publish.",
                'icon' => 'bi-check-circle',
                'type' => 'success',
            ]
            : [
                'title' => 'Changes requested on publication proof',
                'message' => "The author requested changes to the publication proof for \"{$manuscript->title}\".",
                'icon' => 'bi-chat-left-text',
                'type' => 'warning',
            ];

        $this->notifyPermissionHolders('journal', 'manage-workflow', new AppNotification(
            title: $notification['title'],
            message: $notification['message'],
            url: route('journal.manuscripts.show', $manuscript),
            icon: $notification['icon'],
            type: $notification['type'],
        ));

        return back()->with(
            'success',
            $data['decision'] === 'approved'
                ? 'Thanks — you approved the publication proof.'
                : 'Your comments were sent to the Journal Manager.'
        );
    }

    /**
     * Journal Manager / EIC: publish an accepted manuscript and mint
     * its DOI. (Placeholder DOI format — swap in a real Crossref/DOI
     * registration call here when that integration is built.)
     */
    public function publish(Request $request, Manuscript $manuscript)
    {
        $this->authorizePermission('manage-workflow');

        abort_unless($manuscript->status === 'accepted', 422, 'Only accepted manuscripts can be published.');

        abort_unless(
            $manuscript->isFeeSettled(),
            422,
            'The publication fee has not been paid yet. The author must complete payment before this manuscript can be published.'
        );

        abort_unless(
            $manuscript->isProofApproved(),
            422,
            'The author has not approved the publication proof yet. Send the proof and wait for their approval before publishing.'
        );

        $data = $request->validate([
            // Optional: the DOI-stamped version-of-record, if it
            // differs from the plain proof the author approved (e.g.
            // a typesetter adds the citation footer after the DOI is
            // minted). Content must match what was approved — this
            // is not a way to slip in unapproved changes.
            'published_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $manuscript->update([
            'status' => 'published',
            'doi' => $manuscript->doi ?: '10.0000/ora.journal.'.Str::padLeft((string) $manuscript->id, 6, '0'),
            'published_file' => $request->hasFile('published_file')
                ? $request->file('published_file')->store('manuscript-published', 'public')
                : $manuscript->proof_file,
            'published_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        $manuscript->author?->notify(new AppNotification(
            title: 'Manuscript published',
            message: "\"{$manuscript->title}\" has been published. DOI: {$manuscript->doi}",
            url: route('journal.manuscripts.show', $manuscript),
            icon: 'bi-journal-check',
            type: 'success',
        ));

        return back()->with('success', 'Manuscript published.');
    }

    /**
     * Replace a manuscript's co-author rows wholesale — simpler and
     * safer than diffing against existing IDs for what's normally a
     * handful of rows, and avoids ever mixing up which row is which
     * across an add/remove/reorder edit on the form.
     *
     * Blank rows (no name typed in) are silently dropped: the author
     * may have clicked "Add Co-Author" and then changed their mind,
     * and that shouldn't be a validation error.
     */
    protected function syncCoAuthors(Manuscript $manuscript, array $rows): void
    {
        $manuscript->coAuthors()->delete();

        collect($rows)
            ->filter(fn ($row) => filled($row['full_name'] ?? null))
            ->values()
            ->each(function (array $row, int $index) use ($manuscript) {
                ManuscriptCoAuthor::create([
                    'manuscript_id' => $manuscript->id,
                    'full_name' => $row['full_name'],
                    'email' => $row['email'] ?? null,
                    'affiliation' => $row['affiliation'] ?? null,
                    'orcid' => $row['orcid'] ?? null,
                    'position' => $index,
                ]);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization helpers
    |--------------------------------------------------------------------------
    */

    protected function isEditorial(User $user): bool
    {
        return $user->isSuperAdmin()
            || $user->hasModulePermission('journal', 'manage-workflow')
            || $user->hasModulePermission('journal', 'make-final-decision')
            || $user->hasModulePermission('journal', 'screen-submissions');
    }

    /**
     * Double-blind peer review gate: true only for someone whose
     * entire relationship to this module is "Reviewer" — not an
     * editorial role, not a Super Admin. That's the one role the
     * author's identity must never reach, since reviewers are meant
     * to judge the work, not the name attached to it.
     */
    protected function isReviewerOnly(User $user): bool
    {
        return ! $this->isEditorial($user)
            && ! $user->isSuperAdmin()
            && $user->hasModulePermission('journal', 'review-manuscripts');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('journal', $permission),
            403,
            'You do not have permission to do this.'
        );
    }

    protected function authorizeView(Manuscript $manuscript): void
    {
        $user = Auth::user();

        if ($manuscript->author_id === $user->id) {
            return;
        }

        // A draft is only visible to its author, full stop — it hasn't
        // been pushed into the workflow yet, so no editorial role or
        // reviewer has any reason to see it.
        if ($manuscript->status === 'draft') {
            abort(404);
        }

        if ($user->isSuperAdmin() || $this->isEditorial($user)) {
            return;
        }

        if ($manuscript->reviews()->where('reviewer_id', $user->id)->exists()) {
            return;
        }

        abort(403, 'You do not have access to this manuscript.');
    }
}
