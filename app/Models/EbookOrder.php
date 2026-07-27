<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A reader's purchase of a 'for_sale' eBook — see the migration for
 * why this is deliberately separate from EbookPayment (the author's
 * Book Processing Charge).
 */
class EbookOrder extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'amount',
        'currency',
        'gateway',
        'method',
        'status',
        'transaction_ref',
        'notes',
        'gateway_response',
        'download_count',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
