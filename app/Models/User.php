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
 * Physical Library: the branch(es) this staff member is explicitly
 * scoped to (Cataloger, Inventory Manager, Librarian (Physical),
 * Acquisition Officer). See canAccessLibraryBranch() — having zero
 * rows here means "every branch", not "no branches".
 */
public function libraryBranches()
{
    return $this->belongsToMany(LibraryBranch::class, 'library_branch_staff', 'user_id', 'branch_id')
        ->withPivot('assigned_by')
        ->withTimestamps();
}

/**
 * Digital Bookstore: every purchase this reader has made, completed
 * or otherwise. See EbookOrder / "My Digital Library".
 */
public function ebookOrders()
{
    return $this->hasMany(EbookOrder::class);
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

/*
|--------------------------------------------------------------------------
| Physical Library branch scoping
|--------------------------------------------------------------------------
|
| Whether a staff member can act on every branch or just some comes
| down to ONE thing: do they have any rows in library_branch_staff?
|
|   - No rows at all ("unscoped")   -> every branch. This is the
|     default for a Library Manager, who isn't tied to one location.
|   - One or more rows ("scoped")   -> only those branches, no matter
|     what permissions the role carries. A Branch Manager holds the
|     same permissions as a Library Manager but is always assigned to
|     a branch, so this is what keeps them fenced to it.
|
| Super Admin is the only universal override.
|
*/

public function hasLibraryBranchScope(): bool
{
    return $this->libraryBranches()->exists();
}

public function canAccessLibraryBranch(?int $branchId): bool
{
    if (! $branchId) {
        return true;
    }

    if ($this->isSuperAdmin()) {
        return true;
    }

    if (! $this->hasLibraryBranchScope()) {
        return true;
    }

    return $this->libraryBranches()->where('library_branches.id', $branchId)->exists();
}

/**
 * IDs of every branch this user may act on — null means "all of
 * them", so callers should treat null as "don't filter" rather than
 * "match nothing".
 */
public function accessibleLibraryBranchIds(): ?array
{
    if ($this->isSuperAdmin() || ! $this->hasLibraryBranchScope()) {
        return null;
    }

    return $this->libraryBranches()->pluck('library_branches.id')->all();
}

/**
 * The single branch this staff member is assigned to, or null if
 * they're unscoped (every branch). The admin/users screen enforces
 * one branch per user even though library_branch_staff can technically
 * hold more than one row.
 */
public function libraryBranch(): ?LibraryBranch
{
    return $this->libraryBranches->first();
}
}