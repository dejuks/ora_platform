<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleRevision extends Model
{
    protected $fillable = [
        'article_id',
        'editor_id',
        'title',
        'content',
        'edit_summary',
        'ip_address',
        'user_agent',
        'is_suppressed',
        'suppressed_by',
        'suppressed_at',
        'suppression_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_suppressed' => 'boolean',
            'suppressed_at' => 'datetime',
        ];
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function suppressedBy()
    {
        return $this->belongsTo(User::class, 'suppressed_by');
    }
}
