<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'protection_level',
        'author_id',
        'last_edited_by',
        'protected_by',
        'protected_at',
        'published_at',
        'deleted_by',
        'restored_by',
        'restored_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'protected_at' => 'datetime',
            'published_at' => 'datetime',
            'restored_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'draft' => 'Draft',
        'published' => 'Published',
    ];

    public const PROTECTION_LEVELS = [
        'none' => 'Unprotected',
        'semi' => 'Semi-protected',
        'full' => 'Fully protected',
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

    public function lastEditedBy()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function protectedBy()
    {
        return $this->belongsTo(User::class, 'protected_by');
    }

    public function revisions()
    {
        return $this->hasMany(ArticleRevision::class)->latest();
    }

    public function deletionDiscussions()
    {
        return $this->hasMany(ArticleDeletionDiscussion::class);
    }

    public function openDeletionDiscussion()
    {
        return $this->hasOne(ArticleDeletionDiscussion::class)->where('status', 'open');
    }

    public function categories()
    {
        return $this->belongsToMany(WikiCategory::class, 'article_wiki_category');
    }

    public function editRequests()
    {
        return $this->hasMany(ArticleEditRequest::class);
    }

    /**
     * The requester's still-usable, owner-approved one-time edit
     * pass for this article, if they have one.
     */
    public function consumableEditRequestFor(User $user): ?ArticleEditRequest
    {
        return $this->editRequests()
            ->where('requester_id', $user->id)
            ->approved()
            ->whereNull('used_at')
            ->latest()
            ->first();
    }

    public function pendingEditRequestFor(User $user): ?ArticleEditRequest
    {
        return $this->editRequests()
            ->where('requester_id', $user->id)
            ->pending()
            ->latest()
            ->first();
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

    public function protectionLabel(): string
    {
        return self::PROTECTION_LEVELS[$this->protection_level] ?? $this->protection_level;
    }

    public function isFullyProtected(): bool
    {
        return $this->protection_level === 'full';
    }

    /**
     * Semi-protection blocks nobody in this module today (every
     * member already holds at least the Registered Editor role to
     * get module access), but the flag is here for when anonymous /
     * unconfirmed editing is introduced.
     */
    public function isSemiProtected(): bool
    {
        return $this->protection_level === 'semi';
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
        return $query->whereHas('categories', fn ($q) => $q->where('slug', $categorySlug));
    }
}
