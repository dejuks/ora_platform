<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A reader's purchase of a priced LibraryDigitalResource — mirrors
 * EbookOrder for the same reasoning: one row per checkout attempt,
 * 'completed' is what unlocks the download.
 */
class LibraryDigitalResourceOrder extends Model
{
    protected $fillable = [
        'library_digital_resource_id',
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

    public function resource()
    {
        return $this->belongsTo(LibraryDigitalResource::class, 'library_digital_resource_id');
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
