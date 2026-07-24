<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryLoan extends Model
{
    protected $fillable = [
        'library_book_copy_id',
        'library_member_id',
        'issued_by',
        'returned_to',
        'checked_out_at',
        'due_at',
        'returned_at',
        'renewal_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'checked_out_at' => 'datetime',
            'due_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'active' => 'Active',
        'returned' => 'Returned',
        'lost' => 'Lost',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function copy()
    {
        return $this->belongsTo(LibraryBookCopy::class, 'library_book_copy_id');
    }

    public function member()
    {
        return $this->belongsTo(LibraryMember::class, 'library_member_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedTo()
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function fine()
    {
        return $this->hasOne(LibraryFine::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->due_at->isPast();
    }

    public function daysOverdue(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return (int) $this->due_at->diffInDays(now());
    }

    public function canRenew(int $maxRenewals): bool
    {
        return $this->status === 'active'
            && $this->renewal_count < $maxRenewals
            && ! $this->copy->book->holds()->whereIn('status', ['pending', 'ready'])->exists();
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

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')->where('due_at', '<', now());
    }
}
