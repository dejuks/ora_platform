<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'abstract',
        'keywords',
        'author_id',
        'editor_id',
        'status',
        'manuscript_file',
        'editor_decision_notes',
        'decided_by',
        'processing_fee',
        'payment_status',
        'fee_paid_at',
        'waiver_requested',
        'waiver_reason',
        'cleared_by',
        'cleared_at',
        'isbn',
        'doi',
        'ebook_pdf',
        'ebook_epub',
        'cover_image',
        'access_type',
        'price',
        'is_purchasable',
        'embargo_until',
        'produced_by',
        'submitted_at',
        'decided_at',
        'published_at',
        'downloads_count',
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
            'cleared_at' => 'datetime',
            'embargo_until' => 'datetime',
            'processing_fee' => 'decimal:2',
            'waiver_requested' => 'boolean',
            'price' => 'decimal:2',
            'is_purchasable' => 'boolean',
        ];
    }

    /**
     * Every status a book can be in, in workflow order.
     */
    public const STATUSES = [
        'submitted' => 'Submitted',
        'screening' => 'Editorial Screening',
        'desk_rejected' => 'Desk Rejected',
        'under_review' => 'Under Peer Review',
        'minor_revision' => 'Minor Revision',
        'major_revision' => 'Major Revision',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'financial_clearance' => 'Awaiting Financial Clearance',
        'in_production' => 'In Digital Production',
        'published' => 'Published',
    ];

    public const ACCESS_TYPES = [
        'open_access' => 'Open Access',
        'restricted' => 'Restricted (registered readers only)',
        'embargoed' => 'Embargoed',
        'for_sale' => 'For Sale (purchase required)',
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

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function clearedBy()
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }

    public function producedBy()
    {
        return $this->belongsTo(User::class, 'produced_by');
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function payments()
    {
        return $this->hasMany(EbookPayment::class);
    }

    /**
     * Reader purchases of this title (only relevant when access_type
     * is 'for_sale') — see EbookOrder.
     */
    public function orders()
    {
        return $this->hasMany(EbookOrder::class);
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
     * The full submission-to-publishing pipeline, as steps for display
     * (e.g. the progress stepper on the book page). Each step comes
     * back with a 'state':
     *
     *   - complete: already passed through this step
     *   - current:  the book is here right now (green)
     *   - upcoming: hasn't reached this step yet (gray)
     *   - warning:  paused here pending author action (amber)
     *   - danger:   stopped here, rejected (red)
     *
     * 'desk_rejected', 'minor_revision', 'major_revision', and
     * 'rejected' aren't steps of their own — they're exception states
     * layered onto the happy-path step they interrupted, so the
     * stepper always shows one continuous line rather than a dead
     * branch.
     */
    public function workflowSteps(): array
    {
        $steps = [
            'submitted' => 'Submitted',
            'screening' => 'Editorial Screening',
            'under_review' => 'Peer Review',
            'accepted' => 'Accepted',
            'financial_clearance' => 'Financial Clearance',
            'in_production' => 'In Production',
            'published' => 'Published',
        ];

        $order = array_keys($steps);

        $exceptions = [
            'desk_rejected' => ['at' => 'screening', 'state' => 'danger', 'label' => 'Desk Rejected'],
            'minor_revision' => ['at' => 'under_review', 'state' => 'warning', 'label' => 'Minor Revision Requested'],
            'major_revision' => ['at' => 'under_review', 'state' => 'warning', 'label' => 'Major Revision Requested'],
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

    public function accessTypeLabel(): string
    {
        return self::ACCESS_TYPES[$this->access_type] ?? $this->access_type;
    }

    /**
     * Every status a book can be re-opened from — i.e. the workflow
     * "paused" waiting on the author, and the author is allowed to
     * edit their content and push it back into play.
     *
     * Whenever ANY actor in the workflow (Book Editor screening,
     * Book Editor editorial decision, etc.) moves a book to
     * 'minor_revision', 'major_revision', 'desk_rejected', or
     * 'rejected' — every outcome except 'accepted' — the author must
     * revise and resubmit before the book can move again. Only
     * 'accepted' skips this and goes straight to financial clearance.
     */
    public const REVISABLE_STATUSES = ['submitted', 'desk_rejected', 'minor_revision', 'major_revision', 'rejected'];

    public function isEditable(): bool
    {
        return in_array($this->status, self::REVISABLE_STATUSES);
    }

    /**
     * Where a resubmission sends the book, depending on which stage
     * it was paused at:
     *
     *   - desk_rejected            -> back to 'submitted' (re-enters
     *                                 Book Editor screening from
     *                                 scratch — it never made it past
     *                                 screening).
     *   - minor_revision           -> back to 'under_review' (the same
     *   - major_revision              reviewers get a fresh round on
     *                                 the revised manuscript, whether
     *                                 the editorial decision was a
     *                                 minor or a major revision,
     *                                 rather than starting the
     *                                 reviewer assignment over).
     *   - rejected                 -> back to 'submitted' (a final
     *                                 reject is the most severe stage,
     *                                 so a resubmission is treated as
     *                                 a brand new attempt through the
     *                                 full pipeline).
     *   - submitted                -> stays 'submitted' (just an edit
     *                                 before screening has even
     *                                 started).
     */
    public function nextStatusAfterResubmission(): string
    {
        return match ($this->status) {
            'minor_revision', 'major_revision' => 'under_review',
            'desk_rejected', 'rejected' => 'submitted',
            default => 'submitted',
        };
    }

    /**
     * Whether the Book Processing Charge has been settled (paid or
     * waived) — the gate the Finance & Operations Officer checks
     * before granting financial clearance.
     */
    public function isFeeSettled(): bool
    {
        return $this->processing_fee <= 0 || in_array($this->payment_status, ['paid', 'waived']);
    }

    /**
     * Whether a reader can access this book right now, given its
     * access type and (for embargoed titles) whether the embargo has
     * lifted yet. A 'for_sale' title is never freely readable here —
     * see isPurchasedBy() for whether a specific reader has bought it.
     */
    public function isReadableNow(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->access_type === 'for_sale') {
            return false;
        }

        if ($this->access_type === 'embargoed' && $this->embargo_until && $this->embargo_until->isFuture()) {
            return false;
        }

        return true;
    }

    /**
     * Whether the given user (or null for a guest) already owns this
     * 'for_sale' title — the gate the download route and the
     * storefront's "Buy" vs "Download" button both check.
     */
    public function isPurchasedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->orders()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->exists();
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

    public function scopeReadableNow($query)
    {
        return $query->published()
            ->where('access_type', '!=', 'for_sale')
            ->where(function ($q) {
                $q->where('access_type', '!=', 'embargoed')
                    ->orWhereNull('embargo_until')
                    ->orWhere('embargo_until', '<=', now());
            });
    }

    /**
     * Titles currently on sale in the storefront.
     */
    public function scopeForSale($query)
    {
        return $query->published()
            ->where('access_type', 'for_sale')
            ->where('is_purchasable', true);
    }
}
