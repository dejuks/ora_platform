<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'publisher',
        'publication_year',
        'edition',
        'call_number',
        'subject',
        'category_id',
        'description',
        'status',
        'cataloged_by',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'publication_year' => 'integer',
        ];
    }

    public const STATUSES = [
        'pending_acquisition' => 'Pending Acquisition',
        'active' => 'Active',
        'withdrawn' => 'Withdrawn',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function copies()
    {
        return $this->hasMany(LibraryBookCopy::class);
    }

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }

    public function holds()
    {
        return $this->hasMany(LibraryHold::class);
    }

    public function catalogedBy()
    {
        return $this->belongsTo(User::class, 'cataloged_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function availableCopiesCount(): int
    {
        return $this->copies()->where('status', 'available')->count();
    }

    public function hasAvailableCopy(): bool
    {
        return $this->availableCopiesCount() > 0;
    }

    public function pendingHoldsCount(): int
    {
        return $this->holds()->whereIn('status', ['pending', 'ready'])->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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
        return $query->where('title', 'like', $letter.'%');
    }
}
