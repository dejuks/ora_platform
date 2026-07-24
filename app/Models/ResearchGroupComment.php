<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchGroupComment extends Model
{
    protected $fillable = [
        'research_group_post_id',
        'user_id',
        'body',
        'status',
    ];

    public function post()
    {
        return $this->belongsTo(ResearchGroupPost::class, 'research_group_post_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
