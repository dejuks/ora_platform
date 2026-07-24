<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchGroupMember extends Model
{
    protected $fillable = [
        'research_group_id',
        'user_id',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function group()
    {
        return $this->belongsTo(ResearchGroup::class, 'research_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
