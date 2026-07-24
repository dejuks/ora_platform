<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'module_id',
        'name',
        'slug',
        'description',
        'is_admin_role',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_admin_role' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_module_roles')
            ->withPivot(['module_id', 'is_active'])
            ->wherePivot('is_active', true)
            ->withTimestamps();
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissions()->where('slug', $slug)->exists();
    }
}