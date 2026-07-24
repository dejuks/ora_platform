<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearcherAnnouncement extends Model
{
    protected $fillable = [
        'title',
        'type',
        'body',
        'location',
        'link_url',
        'event_date',
        'submission_deadline',
        'status',
        'published_by',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'submission_deadline' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public const TYPES = [
        'call_for_papers' => 'Call for Papers',
        'conference' => 'Conference',
        'event' => 'Event',
        'news' => 'News / Update',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
