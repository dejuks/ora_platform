<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchConnection extends Model
{
    protected $fillable = [
        'requester_id',
        'addressee_id',
        'status',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'declined' => 'Declined',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function addressee()
    {
        return $this->belongsTo(User::class, 'addressee_id');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * The other user in this connection, relative to the given user.
     */
    public function otherUser(int $userId): ?User
    {
        return $this->requester_id === $userId ? $this->addressee : $this->requester;
    }
}
