<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryHold extends Model
{
    protected $fillable = [
        'library_book_id',
        'library_member_id',
        'library_book_copy_id',
        'status',
        'requested_at',
        'ready_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'ready_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'ready' => 'Ready for Pickup',
        'fulfilled' => 'Fulfilled',
        'cancelled' => 'Cancelled',
        'expired' => 'Expired',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function book()
    {
        return $this->belongsTo(LibraryBook::class, 'library_book_id');
    }

    public function member()
    {
        return $this->belongsTo(LibraryMember::class, 'library_member_id');
    }

    public function reservedCopy()
    {
        return $this->belongsTo(LibraryBookCopy::class, 'library_book_copy_id');
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
}
