<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryResourcePurchase extends Model
{
    protected $fillable = [
        'library_digital_resource_id',
        'user_id',
        'pricing_plan_id',
        'amount',
        'currency',
        'gateway',
        'method',
        'status',
        'transaction_ref',
        'gateway_response',
        'notes',
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

    public function plan()
    {
        return $this->belongsTo(LibraryPricingPlan::class, 'pricing_plan_id');
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

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
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
