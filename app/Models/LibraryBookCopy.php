<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBookCopy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'library_book_id',
        'barcode',
        'shelf_location',
        'condition',
        'status',
        'tagged_by',
        'notes',
        'created_by',
        'updated_by',
    ];

    public const STATUSES = [
        'pending_acquisition' => 'Pending Acquisition',
        'available' => 'Available',
        'on_loan' => 'On Loan',
        'on_hold' => 'On Hold',
        'lost' => 'Lost',
        'damaged' => 'Damaged',
        'withdrawn' => 'Withdrawn',
    ];

    public const CONDITIONS = [
        'good' => 'Good',
        'worn' => 'Worn',
        'damaged' => 'Damaged',
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

    public function loans()
    {
        return $this->hasMany(LibraryLoan::class);
    }

    public function activeLoan()
    {
        return $this->hasOne(LibraryLoan::class)->where('status', 'active');
    }

    public function holds()
    {
        return $this->hasMany(LibraryHold::class, 'library_book_copy_id');
    }

    public function taggedBy()
    {
        return $this->belongsTo(User::class, 'tagged_by');
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

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
