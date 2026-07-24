<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'field_of_study',
        'privacy',
        'status',
        'created_by',
        'moderator_id',
    ];

    public const PRIVACY_LEVELS = [
        'public' => 'Public (anyone can join)',
        'private' => 'Private (moderator approval required)',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function members()
    {
        return $this->hasMany(ResearchGroupMember::class);
    }

    public function approvedMembers()
    {
        return $this->members()->where('status', 'approved');
    }

    public function posts()
    {
        return $this->hasMany(ResearchGroupPost::class)->latest();
    }

    public function memberCount(): int
    {
        return $this->approvedMembers()->count();
    }

    public function isMember(int $userId): bool
    {
        return $this->approvedMembers()->where('user_id', $userId)->exists();
    }

    public function isModerator(User $user): bool
    {
        return $user->isSuperAdmin()
            || $this->moderator_id === $user->id
            || $user->hasModulePermission('researcher', 'manage-network-groups');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
