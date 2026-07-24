<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryMember extends Model
{
    protected $fillable = [
        'user_id',
        'membership_no',
        'member_type',
        'status',
        'max_active_loans',
        'joined_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public const MEMBER_TYPES = [
        'student' => 'Student',
        'staff' => 'Staff',
        'faculty' => 'Faculty',
        'external' => 'External',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'suspended' => 'Suspended',
        'expired' => 'Expired',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loans()
    {
        return $this->hasMany(LibraryLoan::class);
    }

    public function holds()
    {
        return $this->hasMany(LibraryHold::class);
    }

    public function fines()
    {
        return $this->hasMany(LibraryFine::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function activeLoansCount(): int
    {
        return $this->loans()->where('status', 'active')->count();
    }

    public function hasUnpaidFines(): bool
    {
        return $this->fines()->where('status', 'unpaid')->exists();
    }

    /**
     * Whether this member is in good standing to borrow: active
     * membership, under their loan limit, and no outstanding fines.
     */
    public function canBorrow(): bool
    {
        return $this->status === 'active'
            && $this->activeLoansCount() < $this->max_active_loans
            && ! $this->hasUnpaidFines();
    }
}
