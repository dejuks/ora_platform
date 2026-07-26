<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleEditRequest extends Model
{
    protected $fillable = [
        'article_id',
        'requester_id',
        'message',
        'status',
        'decided_by',
        'decided_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Approved and not yet spent on a save.
     */
    public function isConsumable(): bool
    {
        return $this->status === 'approved' && $this->used_at === null;
    }

    public function markUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
