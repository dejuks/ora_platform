<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [

        'employee_no',

        'first_name',

        'middle_name',

        'last_name',

        'username',

        'email',

        'phone',

        'gender',

        'date_of_birth',

        'profile_photo',

        'notify_in_app',

        'notify_email',

        'password',

        'status',

        'is_super_admin',

        'email_verified',

        'email_verified_at',

        'last_login_at',

        'last_login_ip',

        'failed_login_attempts',

        'locked_until',

        'created_by',

        'updated_by',
    ];

    protected $hidden = [

        'password',

        'remember_token',
    ];

    protected function casts(): array
    {
        return [

            'is_super_admin' => 'boolean',

            'notify_in_app' => 'boolean',

            'notify_email' => 'boolean',

            'email_verified' => 'boolean',

            'email_verified_at' => 'datetime',

            'last_login_at' => 'datetime',

            'locked_until' => 'datetime',

            'date_of_birth' => 'date',

            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            "{$this->first_name} {$this->middle_name} {$this->last_name}"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }



/**
 * Researcher Network: this user's profile.
 */
public function researcherProfile()
{
    return $this->hasOne(ResearcherProfile::class);
}

public function libraryMember()
{
    return $this->hasOne(LibraryMember::class);
}

/**
 * This user's audit trail — powers the Activity Log page.
 */
public function activityLogs()
{
    return $this->hasMany(ActivityLog::class)->latest();
}

/**
 * Every role this user actively holds, across every module.
 */
public function moduleRoles()
{
    return $this->belongsToMany(Role::class, 'user_module_roles')
        ->withPivot(['id', 'module_id', 'is_active'])
        ->wherePivot('is_active', true)
        ->withTimestamps();
}

/*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
|
| Super Admin sits above every module and always passes every check
| below. Below that, access is entirely role-driven: a module
| defines its own roles (Editor-in-Chief, Reviewer, Author, ...),
| each role carries a set of permissions, and a user can hold any
| number of roles — even several in the same module at once.
|
*/

public function isSuperAdmin(): bool
{
    return (bool) $this->is_super_admin;
}

public function hasModuleAccess(string $moduleCode): bool
{
    if ($this->isSuperAdmin()) {
        return true;
    }

    return $this->moduleRoles()
        ->whereHas('module', fn ($q) => $q->where('code', $moduleCode))
        ->exists();
}

public function isModuleAdmin(string $moduleCode): bool
{
    if ($this->isSuperAdmin()) {
        return true;
    }

    return $this->moduleRoles()
        ->where('is_admin_role', true)
        ->whereHas('module', fn ($q) => $q->where('code', $moduleCode))
        ->exists();
}

public function hasModulePermission(string $moduleCode, string $permissionSlug): bool
{
    if ($this->isSuperAdmin()) {
        return true;
    }

    return $this->moduleRoles()
        ->whereHas('module', fn ($q) => $q->where('code', $moduleCode))
        ->whereHas('permissions', fn ($q) => $q->where('slug', $permissionSlug))
        ->exists();
}

public function rolesInModule(string $moduleCode)
{
    return $this->moduleRoles()
        ->whereHas('module', fn ($q) => $q->where('code', $moduleCode))
        ->get();
}
}