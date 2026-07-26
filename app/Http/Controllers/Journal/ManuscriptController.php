<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use App\Models\ManuscriptReview;
use App\Models\JournalSetting;
use App\Models\User;
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

        return view('modules.journal.manuscripts.index', compact('manuscripts'));
    }

    public function create()
    {
        return view('modules.journal.manuscripts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            // A draft can be saved without a file yet; pushing it into
            // the review workflow requires one.
            'manuscript_file' => ['required_if:action,submit', 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'action' => ['required', 'in:draft,submit'],
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

        $manuscript = Manuscript::create($data);

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

        $manuscript->load(['author', 'associateEditor', 'decidedBy', 'reviews.reviewer']);

        $reviewers = $this->isEditorial(Auth::user())
            ? User::whereHas('moduleRoles', function ($q) {
                $q->whereHas('module', fn ($m) => $m->where('code', 'journal'))
                    ->where('slug', 'reviewer');
            })->get()
            : collect();

        return view('modules.journal.manuscripts.show', compact('manuscript', 'reviewers'));
    }

    /**
     * Author: edit form for their own manuscript, only while it's
     * sitting at a stage that allows it (see Manuscript::isEditable()).
     */
    public function edit(Manuscript $manuscript)
    {
        $this->authorizeAuthorEdit($manuscript);

        return view('modules.journal.manuscripts.edit', compact('manuscript'));
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
            'manuscript_file' => [
                $isDraft ? 'required_if:action,submit' : 'nullable',
                'nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240',
            ],
            'action' => [$isDraft ? 'required' : 'nullable', 'in:draft,submit'],
        ]);

        $data['abstract'] = Purifier::clean($data['abstract'], 'manuscript_abstract');

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
                'manuscript_file' => $data['manuscript_file'] ?? $manuscript->manuscript_file,
                'status' => $isSubmit ? 'submitted' : 'draft',
                'submitted_at' => $isSubmit ? now() : null,
                'updated_by' => Auth::id(),
            ]);

            $message = $isSubmit
                ? 'Manuscript submitted successfully.'
                : 'Draft saved. Only you can see it until you push it for review.';

            return redirect()
                ->route('journal.manuscripts.show', $manuscript)
                ->with('success', $message);
        }

        DB::transaction(function () use ($manuscript, $data) {
            $wasDeskRejectedOrRejected = in_array($manuscript->status, ['desk_rejected', 'rejected']);
            $wasRevisionRequested = $manuscript->status === 'revision_requested';

            $newStatus = $manuscript->nextStatusAfterResubmission();

            $manuscript->update([
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'keywords' => $data['keywords'] ?? null,
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

            if ($wasRevisionRequested) {
                // Same reviewers, fresh round on the revised file —
                // clear their prior verdicts so the revised manuscript
                // shows up as pending on their dashboard again.
                $manuscript->reviews()->whereIn('status', ['assigned', 'submitted'])->get()->each(function ($review) {
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
                // Re-enters screening cold: clear the prior Associate
                // Editor assignment so it lands back in the general
                // screening queue rather than looking "already handled".
                $manuscript->associate_editor_id = null;
                $manuscript->save();
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

        return back()->with('success', 'Decision recorded.');
    }

    /**
     * Journal Manager / EIC: publish an accepted manuscript and mint
     * its DOI. (Placeholder DOI format — swap in a real Crossref/DOI
     * registration call here when that integration is built.)
     */
    public function publish(Manuscript $manuscript)
    {
        $this->authorizePermission('manage-workflow');

        abort_unless($manuscript->status === 'accepted', 422, 'Only accepted manuscripts can be published.');

        abort_unless(
            $manuscript->isFeeSettled(),
            422,
            'The publication fee has not been paid yet. The author must complete payment before this manuscript can be published.'
        );

        $manuscript->update([
            'status' => 'published',
            'doi' => $manuscript->doi ?: '10.0000/ora.journal.'.Str::padLeft((string) $manuscript->id, 6, '0'),
            'published_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Manuscript published.');
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
