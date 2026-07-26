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
        'status',
        'manuscript_file',
        'editor_decision_notes',
        'decided_by',
        'doi',
        'publication_fee',
        'payment_status',
        'fee_paid_at',
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

    public function payments()
    {
        return $this->hasMany(JournalPayment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
