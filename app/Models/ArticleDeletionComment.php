<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleDeletionComment extends Model
{
    protected $fillable = [
        'discussion_id',
        'user_id',
        'stance',
        'comment',
    ];

    public const STANCES = [
        'keep' => 'Keep',
        'delete' => 'Delete',
        'comment' => 'Comment',
    ];

    public function discussion()
    {
        return $this->belongsTo(ArticleDeletionDiscussion::class, 'discussion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stanceLabel(): string
    {
        return self::STANCES[$this->stance] ?? $this->stance;
    }
}
