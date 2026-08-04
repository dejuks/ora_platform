<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LibraryBranch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'city',
        'region',
        'address',
        'phone',
        'email',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function copies()
    {
        return $this->hasMany(LibraryBookCopy::class, 'branch_id');
    }

    /**
     * Staff explicitly scoped to this branch (see
     * User::canAccessLibraryBranch() for what "scoped" means — a
     * staff member with no rows in library_branch_staff at all can
     * still reach every branch).
     */
    public function staff()
    {
        return $this->belongsToMany(User::class, 'library_branch_staff', 'branch_id', 'user_id')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function uniqueCode(string $name, ?int $ignoreId = null): string
    {
        $base = Str::upper(Str::slug($name, '')) ?: 'BR';
        $base = substr($base, 0, 10);
        $code = $base;
        $i = 2;

        while (
            static::where('code', $code)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $code = $base.$i;
            $i++;
        }

        return $code;
    }

    public function locationLabel(): string
    {
        return $this->city ? "{$this->name} ({$this->city})" : $this->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
