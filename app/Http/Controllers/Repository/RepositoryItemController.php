<?php

namespace App\Http\Controllers\Repository;

use App\Http\Controllers\Controller;
use App\Models\RepositoryItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * The real Repository Management workflow:
 *
 *   Researcher/Author deposits -> Repository Curator validates &
 *   enriches metadata (Dublin Core), verifies copyright, applies
 *   access control -> Content Reviewer checks academic quality,
 *   plagiarism, and citation accuracy, recommends a decision ->
 *   Repository Administrator makes the final approval and publishes
 *   with a persistent URL and citation-ready metadata.
 *
 * Every action below is gated by the actual permission that role
 * carries in this module (seeded in RoleSeeder), not by job title —
 * so if a Super Admin reassigns who holds "Repository Curator" later,
 * these checks keep working with zero code changes.
 */
class RepositoryItemController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = RepositoryItem::with(['depositor', 'curator', 'contentReviewer'])->latest();

        if ($user->hasModulePermission('repository', 'curate-metadata')
            || $user->hasModulePermission('repository', 'review-repository-submissions')
            || $user->hasModulePermission('repository', 'approve-repository-submissions')
            || $user->isSuperAdmin()) {
            // Curator / Content Reviewer / Administrator / Super Admin see everything.
        } else {
            // Plain Depositor: only their own submissions.
            $query->where('depositor_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $items = $query->paginate(15)->withQueryString();

        return view('modules.repository.items.index', compact('items'));
    }

    public function create()
    {
        return view('modules.repository.items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'authors' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'resource_type' => ['required', 'in:'.implode(',', array_keys(RepositoryItem::RESOURCE_TYPES))],
            'keywords' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'contributors' => ['nullable', 'string', 'max:255'],
            'publication_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:10'],
            'external_identifier' => ['nullable', 'string', 'max:255'],
            'related_identifiers' => ['nullable', 'string', 'max:255'],
            'coverage' => ['nullable', 'string', 'max:255'],
            'rights_statement' => ['nullable', 'string'],
            'bibliographic_references' => ['nullable', 'string'],
            'access_level' => ['required', 'in:'.implode(',', array_keys(RepositoryItem::ACCESS_LEVELS))],
            'embargo_until' => ['nullable', 'date', 'after:today'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,csv,xlsx,zip', 'max:20480'],
        ]);

        $data['file_path'] = $request->file('file')->store('repository-items', 'public');
        $data['language'] = $data['language'] ?? 'en';
        $data['depositor_id'] = Auth::id();
        $data['status'] = 'submitted';
        $data['submitted_at'] = now();
        $data['created_by'] = Auth::id();

        $item = RepositoryItem::create($data);

        return redirect()
            ->route('repository.items.show', $item)
            ->with('success', 'Item deposited successfully and is now awaiting metadata review.');
    }

    public function show(RepositoryItem $item)
    {
        $this->authorizeView($item);

        $item->load(['depositor', 'curator', 'contentReviewer', 'decidedBy']);

        return view('modules.repository.items.show', compact('item'));
    }

    /**
     * Repository Curator: validate & enrich the bibliographic
     * metadata, verify copyright/embargo policy, apply access
     * control, and forward the deposit to content review.
     */
    public function curate(Request $request, RepositoryItem $item)
    {
        $this->authorizePermission('curate-metadata');

        abort_unless(in_array($item->status, ['submitted', 'metadata_review']), 422, 'This item is not awaiting metadata review.');

        $data = $request->validate([
            'controlled_vocabulary' => ['nullable', 'string', 'max:255'],
            'access_level' => ['required', 'in:'.implode(',', array_keys(RepositoryItem::ACCESS_LEVELS))],
            'embargo_until' => ['nullable', 'date', 'after:today'],
            'curator_notes' => ['nullable', 'string'],
            'decision' => ['required', 'in:advance,return'],
        ]);

        $item->update([
            'controlled_vocabulary' => $data['controlled_vocabulary'] ?? $item->controlled_vocabulary,
            'access_level' => $data['access_level'],
            'embargo_until' => $data['embargo_until'] ?? null,
            'copyright_verified' => $request->boolean('copyright_verified'),
            'curator_notes' => $data['curator_notes'] ?? null,
            'curator_id' => Auth::id(),
            'curated_at' => now(),
            'status' => $data['decision'] === 'advance' ? 'content_review' : 'revision_requested',
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Metadata review recorded.');
    }

    /**
     * Content Reviewer: assess academic quality, relevance, and
     * citation/plagiarism integrity, then recommend a decision.
     */
    public function review(Request $request, RepositoryItem $item)
    {
        $this->authorizePermission('review-repository-submissions');

        abort_unless(in_array($item->status, ['content_review']), 422, 'This item is not awaiting content review.');

        $data = $request->validate([
            'reviewer_recommendation' => ['required', 'in:'.implode(',', array_keys(RepositoryItem::RECOMMENDATIONS))],
            'reviewer_notes' => ['nullable', 'string'],
        ]);

        $status = match ($data['reviewer_recommendation']) {
            'approve' => 'recommended',
            'reject' => 'rejected',
            default => 'revision_requested', // minor_revision, major_revision
        };

        $item->update([
            'plagiarism_checked' => $request->boolean('plagiarism_checked'),
            'reviewer_recommendation' => $data['reviewer_recommendation'],
            'reviewer_notes' => $data['reviewer_notes'] ?? null,
            'content_reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'status' => $status,
            'decided_by' => $status === 'rejected' ? Auth::id() : $item->decided_by,
            'decided_at' => $status === 'rejected' ? now() : $item->decided_at,
            'decision_notes' => $status === 'rejected' ? ($data['reviewer_notes'] ?? null) : $item->decision_notes,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Content & citation review recorded.');
    }

    /**
     * Repository Administrator: the final approve / reject /
     * request-revision call, based on the Content Reviewer's
     * recommendation.
     */
    public function decide(Request $request, RepositoryItem $item)
    {
        $this->authorizePermission('approve-repository-submissions');

        abort_unless($item->status === 'recommended', 422, 'This item has not been recommended for a decision yet.');

        $data = $request->validate([
            'decision' => ['required', 'in:approved,rejected,revision_requested'],
            'notes' => ['nullable', 'string'],
        ]);

        $item->update([
            'status' => $data['decision'],
            'decision_notes' => $data['notes'] ?? $item->decision_notes,
            'decided_by' => Auth::id(),
            'decided_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Decision recorded.');
    }

    /**
     * Repository Administrator: publish an approved deposit, assign
     * its persistent URL, and finalize citation-ready metadata.
     */
    public function publish(RepositoryItem $item)
    {
        $this->authorizePermission('approve-repository-submissions');

        abort_unless($item->status === 'approved', 422, 'Only approved items can be published.');

        $item->update([
            'status' => 'published',
            'persistent_url' => $item->persistent_url ?: $this->generatePersistentUrl($item),
            'published_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Item published with a persistent URL.');
    }

    /**
     * Repository Administrator: adjust access controls on a
     * published item (e.g. lift an embargo, switch open/restricted).
     */
    public function updateAccess(Request $request, RepositoryItem $item)
    {
        $this->authorizePermission('manage-repository-access');

        $data = $request->validate([
            'access_level' => ['required', 'in:'.implode(',', array_keys(RepositoryItem::ACCESS_LEVELS))],
            'embargo_until' => ['nullable', 'date'],
        ]);

        $item->update([
            'access_level' => $data['access_level'],
            'embargo_until' => $data['embargo_until'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Access policy updated.');
    }

    /**
     * Download the deposited file. Open Access items are downloadable
     * by anyone; Restricted items require login; a live embargo blocks
     * everyone regardless of access level.
     */
    public function download(RepositoryItem $item)
    {
        abort_unless($item->status === 'published', 404);

        if ($item->embargo_until && $item->embargo_until->isFuture()) {
            abort(403, 'This item is under embargo until '.$item->embargo_until->format('M d, Y').'.');
        }

        if ($item->access_level === 'restricted') {
            abort_unless(Auth::check(), 401, 'Please sign in to download this restricted item.');
        }

        abort_unless($item->file_path, 404, 'No file available for this item.');

        $item->increment('downloads_count');

        return \Illuminate\Support\Facades\Storage::disk('public')->download($item->file_path, Str::slug($item->title).'.'.pathinfo($item->file_path, PATHINFO_EXTENSION));
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function generatePersistentUrl(RepositoryItem $item): string
    {
        return route('repository.public.show', $item);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization helpers
    |--------------------------------------------------------------------------
    */

    protected function isRepositoryStaff(User $user): bool
    {
        return $user->isSuperAdmin()
            || $user->hasModulePermission('repository', 'curate-metadata')
            || $user->hasModulePermission('repository', 'review-repository-submissions')
            || $user->hasModulePermission('repository', 'approve-repository-submissions')
            || $user->hasModulePermission('repository', 'manage-repository-access');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('repository', $permission),
            403,
            'You do not have permission to do this.'
        );
    }

    protected function authorizeView(RepositoryItem $item): void
    {
        $user = Auth::user();

        if ($user->isSuperAdmin() || $this->isRepositoryStaff($user)) {
            return;
        }

        if ($item->depositor_id === $user->id) {
            return;
        }

        abort(403, 'You do not have access to this item.');
    }
}
