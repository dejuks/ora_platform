<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookSetting extends Model
{
    protected $fillable = [
        'processing_fee',
        'currency',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'processing_fee' => 'decimal:2',
        ];
    }

    /**
     * There is exactly one settings row in effect. Creates the
     * default one (from the old config/ebook.php values) on first
     * use, so the rest of the module never has to worry about it
     * being missing.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'processing_fee' => config('ebook.processing_fee', 75.00),
            'currency' => config('ebook.currency', 'ETB'),
        ]);
    }
}
