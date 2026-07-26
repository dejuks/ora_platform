<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'require_email_verification',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'require_email_verification' => 'boolean',
        ];
    }

    /**
     * There is exactly one settings row in effect. Creates the
     * default one on first use, so nothing else has to worry about
     * it being missing.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'require_email_verification' => true,
        ]);
    }
}
