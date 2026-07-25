<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WikiBlock extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'parent_block_id',
        'is_autoblock',
        'blocked_by',
        'reason',
        'expires_at',
        'lifted_by',
        'lifted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'lifted_at' => 'datetime',
            'is_autoblock' => 'boolean',
        ];
    }

    /**
     * The IP block that spawned this autoblock on a registered account.
     */
    public function parentBlock()
    {
        return $this->belongsTo(self::class, 'parent_block_id');
    }

    /**
     * Autoblocks on accounts that were logged in from a blocked IP.
     */
    public function autoblocks()
    {
        return $this->hasMany(self::class, 'parent_block_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function liftedBy()
    {
        return $this->belongsTo(User::class, 'lifted_by');
    }

    public function target(): string
    {
        return $this->user_id ? ($this->user->full_name ?? "User #{$this->user_id}") : $this->ip_address;
    }

    public function isActive(): bool
    {
        if ($this->lifted_at) {
            return false;
        }

        return ! $this->expires_at || $this->expires_at->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->whereNull('lifted_at')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
