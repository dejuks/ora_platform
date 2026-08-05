<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manuscript extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'abstract',
        'keywords',
        'author_id',
        'associate_editor_id',
        'category_id',
        'status',
        'manuscript_file',
        'editor_decision_notes',
        'decided_by',
        'doi',
        'publication_fee',
        'payment_status',
        'fee_paid_at',
        'proof_file',
        'proof_status',
        'proof_message',
        'proof_feedback',
        'proof_sent_by',
        'proof_sent_at',
        'proof_responded_at',
        'published_file',
        'submitted_at',
        'decided_at',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'decided_at' => 'datetime',
            'published_at' => 'datetime',
            'fee_paid_at' => 'datetime',
            'proof_sent_at' => 'datetime',
            'proof_responded_at' => 'datetime',
            'publication_fee' => 'decimal:2',
        ];
    }

    /**
     * Every status a manuscript can be in, in workflow order.
     */
    public const STATUSES = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'screening' => 'Editorial Screening',
        'desk_rejected' => 'Desk Rejected',
        'under_review' => 'Under Peer Review',
        'revision_requested' => 'Revision Requested',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'published' => 'Published',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function associateEditor()
    {
        return $this->belongsTo(User::class, 'associate_editor_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function reviews()
    {
        return $this->hasMany(ManuscriptReview::class);
    }

    public function coAuthors()
    {
        return $this->hasMany(ManuscriptCoAuthor::class)->orderBy('position');
    }

    public function payments()
    {
        return $this->hasMany(JournalPayment::class);
    }

    public function category()
    {
        return $this->belongsTo(JournalCategory::class, 'category_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * The full submission-to-publishing pipeline, as steps for display
     * (e.g. a progress stepper on the manuscript page). Each step comes
     * back with a 'state':
     *
     *   - complete: already passed through this step
     *   - current:  the manuscript is here right now (green)
     *   - upcoming: hasn't reached this step yet (gray)
     *   - warning:  paused here pending author action (amber)
     *   - danger:   stopped here, rejected (red)
     *
     * 'desk_rejected', 'revision_requested', and 'rejected' aren't
     * steps of their own — they're exception states layered onto the
     * happy-path step they interrupted, so the stepper always shows
     * one continuous line rather than a dead branch.
     */
    public function workflowSteps(): array
    {
        $steps = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'screening' => 'Editorial Screening',
            'under_review' => 'Peer Review',
            'accepted' => 'Accepted',
            'published' => 'Published',
        ];

        $order = array_keys($steps);

        $exceptions = [
            'desk_rejected' => ['at' => 'screening', 'state' => 'danger', 'label' => 'Desk Rejected'],
            'revision_requested' => ['at' => 'under_review', 'state' => 'warning', 'label' => 'Revision Requested'],
            'rejected' => ['at' => 'accepted', 'state' => 'danger', 'label' => 'Rejected'],
        ];

        $exception = $exceptions[$this->status] ?? null;
        $effectiveKey = $exception['at'] ?? $this->status;
        $currentIndex = array_search($effectiveKey, $order, true);

        $result = [];

        foreach ($order as $i => $key) {
            if ($currentIndex === false || $i < $currentIndex) {
                $state = 'complete';
                $label = $steps[$key];
            } elseif ($i === $currentIndex) {
                $state = $exception['state'] ?? 'current';
                $label = $exception['label'] ?? $steps[$key];
            } else {
                $state = 'upcoming';
                $label = $steps[$key];
            }

            $result[] = ['key' => $key, 'label' => $label, 'state' => $state];
        }

        return $result;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Every status a manuscript can be re-opened from — i.e. the
     * workflow "paused" waiting on the author, and the author is
     * allowed to edit their content and push it back into play.
     */
    public const REVISABLE_STATUSES = ['draft', 'submitted', 'desk_rejected', 'revision_requested', 'rejected'];

    public function isEditable(): bool
    {
        return in_array($this->status, self::REVISABLE_STATUSES);
    }

    /**
     * Where a resubmission sends the manuscript, depending on which
     * stage it was paused at:
     *
     *   - desk_rejected      -> back to 'submitted' (re-enters AE
     *                           screening from scratch — it never made
     *                           it past screening).
     *   - revision_requested -> back to 'under_review' (the EIC already
     *                           let it through screening/review once;
     *                           the same reviewers get a fresh round on
     *                           the revised file rather than starting over).
     *   - rejected           -> back to 'submitted' (a final reject is
     *                           the most severe stage, so a resubmission
     *                           is treated as a brand new attempt through
     *                           the full pipeline).
     *   - submitted          -> stays 'submitted' (just an edit before
     *                           screening has even started).
     */
    public function nextStatusAfterResubmission(): string
    {
        return match ($this->status) {
            'revision_requested' => 'under_review',
            'desk_rejected', 'rejected' => 'submitted',
            default => 'submitted',
        };
    }

    /**
     * Whether the publication fee has been settled (paid or waived) —
     * the single gate the Journal Manager / EIC checks before publish().
     */
    public function isFeeSettled(): bool
    {
        return $this->publication_fee <= 0 || in_array($this->payment_status, ['paid', 'waived']);
    }

    /**
     * Every state the publication proof can be in.
     */
    public const PROOF_STATUSES = [
        'not_sent' => 'Not Sent',
        'sent' => 'Awaiting Author Review',
        'approved' => 'Approved by Author',
        'changes_requested' => 'Changes Requested',
    ];

    public function proofSentBy()
    {
        return $this->belongsTo(User::class, 'proof_sent_by');
    }

    public function proofStatusLabel(): string
    {
        return self::PROOF_STATUSES[$this->proof_status] ?? $this->proof_status;
    }

    /**
     * The single gate publish() checks in addition to the fee: the
     * author must have read the final publication proof and approved
     * it (as opposed to it never being sent, still sitting with the
     * author, or having been kicked back with change requests).
     */
    public function isProofApproved(): bool
    {
        return $this->proof_status === 'approved';
    }

    /**
     * The proof is currently sitting with the author, waiting on
     * their approval or comments.
     */
    public function isProofAwaitingAuthor(): bool
    {
        return $this->proof_status === 'sent';
    }

    /**
     * The one file the public site should ever link to: the
     * version-of-record. Never manuscript_file (the blind
     * peer-review copy) — that file is never guaranteed to carry
     * author details, page numbers, or the DOI footer.
     *
     * Falls back to proof_file for manuscripts published before a
     * separately DOI-stamped file was attached, since the proof is,
     * by definition, the exact content the author already approved.
     */
    public function publicFile(): ?string
    {
        return $this->published_file ?: $this->proof_file;
    }

    /**
     * Full byline in author order: the corresponding author first,
     * then every co-author in the order they were entered. Used
     * anywhere the manuscript's authorship needs to be printed as a
     * single line (public article page, citation block, etc).
     */
    public function byline(): string
    {
        $names = collect([$this->author?->full_name])
            ->merge($this->coAuthors->pluck('full_name'))
            ->filter();

        return $names->implode(', ');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeInCategory($query, string $categorySlug)
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
    }

    /**
     * A-Z filter: titles starting with the given letter (case-insensitive).
     */
    public function scopeTitleStartsWith($query, string $letter)
    {
        return $query->where('title', 'like', $letter . '%');
    }
}
