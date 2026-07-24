<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchGroupPost extends Model
{
    protected $fillable = [
        'research_group_id',
        'user_id',
        'title',
        'body',
        'is_pinned',
        'is_locked',
        'status',
        'moderated_by',
        'moderated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'moderated_at' => 'datetime',
        ];
    }

    public function group()
    {
        return $this->belongsTo(ResearchGroup::class, 'research_group_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(ResearchGroupComment::class)->where('status', 'published')->oldest();
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'published');
    }
}
