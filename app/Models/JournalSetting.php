<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalSetting extends Model
{
    protected $fillable = [
        'publication_fee',
        'currency',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'publication_fee' => 'decimal:2',
        ];
    }

    /**
     * There is exactly one settings row in effect. Creates the
     * default one (from the old config/journal.php values) on first
     * use, so the rest of the module never has to worry about it
     * being missing.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'publication_fee' => config('journal.publication_fee', 50.00),
            'currency' => config('journal.currency', 'ETB'),
        ]);
    }
}
