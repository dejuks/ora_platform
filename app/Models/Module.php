<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'icon',
        'route',
        'description',
        'is_active',
        'is_self_registerable',
        'default_role_slug',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_self_registerable' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * One row per role assignment — a user with two roles in this
     * module appears twice, once per role. Use userCount() below if
     * you need a true count of distinct people.
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_module_roles'
        )
        ->withPivot([
            'id',
            'role_id',
            'is_active',
        ])
        ->wherePivot('is_active', true)
        ->withTimestamps();
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /**
     * The Role this module grants to a user who self-registers into
     * it — resolved from default_role_slug, scoped to this module.
     */
    public function defaultRole()
    {
        if (! $this->default_role_slug) {
            return null;
        }

        return $this->roles()
            ->where('slug', $this->default_role_slug)
            ->first();
    }

    /**
     * True count of distinct people with any role in this module.
     */
    public function userCount(): int
    {
        return User::whereHas('moduleRoles', function ($query) {
            $query->where('roles.module_id', $this->id);
        })->count();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
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

    /**
     * Modules offered as checkboxes on the public registration form
     * and on the "My Modules" self-service page.
     */
    public function scopeSelfRegisterable($query)
    {
        return $query->where('is_active', true)
            ->where('is_self_registerable', true);
    }
}
