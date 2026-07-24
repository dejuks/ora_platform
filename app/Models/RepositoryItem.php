<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepositoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'authors',
        'abstract',
        'resource_type',
        'keywords',
        'publisher',
        'contributors',
        'publication_date',
        'source',
        'language',
        'external_identifier',
        'related_identifiers',
        'coverage',
        'rights_statement',
        'bibliographic_references',
        'file_path',
        'access_level',
        'embargo_until',
        'status',
        'depositor_id',
        'curator_id',
        'curator_notes',
        'controlled_vocabulary',
        'copyright_verified',
        'curated_at',
        'content_reviewer_id',
        'reviewer_recommendation',
        'reviewer_notes',
        'plagiarism_checked',
        'reviewed_at',
        'decided_by',
        'decision_notes',
        'decided_at',
        'persistent_url',
        'submitted_at',
        'published_at',
        'downloads_count',
        'views_count',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'embargo_until' => 'date',
            'copyright_verified' => 'boolean',
            'plagiarism_checked' => 'boolean',
            'curated_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'decided_at' => 'datetime',
            'submitted_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Every status a deposit can be in, in workflow order.
     *
     * submitted           - Researcher/Author has deposited the item
     * metadata_review     - Repository Curator validating & enriching metadata
     * content_review      - Content Reviewer assessing academic/citation integrity
     * revision_requested  - sent back to the depositor
     * recommended         - Content Reviewer recommended a decision to the Administrator
     * rejected            - Repository Administrator rejected the deposit
     * approved            - Repository Administrator approved, awaiting publication
     * published           - live in the repository with a persistent URL
     */
    public const STATUSES = [
        'submitted' => 'Submitted',
        'metadata_review' => 'Metadata Review',
        'content_review' => 'Content & Citation Review',
        'revision_requested' => 'Revision Requested',
        'recommended' => 'Recommended for Approval',
        'rejected' => 'Rejected',
        'approved' => 'Approved',
        'published' => 'Published',
    ];

    public const RESOURCE_TYPES = [
        'article' => 'Journal Article',
        'thesis' => 'Thesis / Dissertation',
        'book_chapter' => 'Book Chapter',
        'conference_paper' => 'Conference Paper',
        'dataset' => 'Dataset',
        'report' => 'Report',
        'working_paper' => 'Working Paper',
        'other' => 'Other',
    ];

    public const ACCESS_LEVELS = [
        'open' => 'Open Access',
        'restricted' => 'Restricted (registered users only)',
    ];

    public const RECOMMENDATIONS = [
        'approve' => 'Recommend Approval',
        'minor_revision' => 'Minor Revision',
        'major_revision' => 'Major Revision',
        'reject' => 'Recommend Rejection',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function depositor()
    {
        return $this->belongsTo(User::class, 'depositor_id');
    }

    public function curator()
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function contentReviewer()
    {
        return $this->belongsTo(User::class, 'content_reviewer_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
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

    public function resourceTypeLabel(): string
    {
        return self::RESOURCE_TYPES[$this->resource_type] ?? $this->resource_type;
    }

    public function accessLevelLabel(): string
    {
        return self::ACCESS_LEVELS[$this->access_level] ?? $this->access_level;
    }

    public function recommendationLabel(): ?string
    {
        return $this->reviewer_recommendation
            ? (self::RECOMMENDATIONS[$this->reviewer_recommendation] ?? $this->reviewer_recommendation)
            : null;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['submitted', 'revision_requested']);
    }

    /**
     * Whether a reader can access the file right now, given the
     * item's access level and (for restricted titles) whether an
     * embargo is still in effect.
     */
    public function isAccessibleNow(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->embargo_until && $this->embargo_until->isFuture()) {
            return false;
        }

        return true;
    }

    /**
     * A citation-ready reference string built from the bibliographic
     * metadata — used on the public record page and in exports.
     */
    public function citation(): string
    {
        $year = $this->publication_date?->format('Y') ?? 'n.d.';

        $parts = array_filter([
            "{$this->authors} ({$year}).",
            "{$this->title}.",
            $this->source,
            $this->publisher,
        ]);

        return implode(' ', $parts);
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

    public function scopeAccessibleNow($query)
    {
        return $query->published()->where(function ($q) {
            $q->whereNull('embargo_until')
                ->orWhere('embargo_until', '<=', now());
        });
    }
}
