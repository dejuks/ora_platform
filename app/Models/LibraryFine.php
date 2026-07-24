<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryFine extends Model
{
    protected $fillable = [
        'library_loan_id',
        'library_member_id',
        'amount',
        'days_overdue',
        'status',
        'collected_by',
        'paid_at',
        'waived_by',
        'waiver_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
        'waived' => 'Waived',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function loan()
    {
        return $this->belongsTo(LibraryLoan::class, 'library_loan_id');
    }

    public function member()
    {
        return $this->belongsTo(LibraryMember::class, 'library_member_id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function waivedBy()
    {
        return $this->belongsTo(User::class, 'waived_by');
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }
}
