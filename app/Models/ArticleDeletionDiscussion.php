<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleDeletionDiscussion extends Model
{
    protected $fillable = [
        'article_id',
        'opened_by',
        'reason',
        'status',
        'closed_by',
        'closing_notes',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'open' => 'Open for discussion',
        'closed_keep' => 'Closed — Kept',
        'closed_delete' => 'Closed — Deleted',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function comments()
    {
        return $this->hasMany(ArticleDeletionComment::class, 'discussion_id')->oldest();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
