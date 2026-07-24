<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryCirculationPolicy extends Model
{
    protected $fillable = [
        'loan_period_days',
        'max_renewals',
        'fine_per_day',
        'hold_expiry_days',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'fine_per_day' => 'decimal:2',
        ];
    }

    /**
     * There is exactly one policy row in effect. Creates the default
     * one on first use so the rest of the module never has to worry
     * about it being missing.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
