<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $modules = Module::orderBy('name')->get();

        $roles = Role::with(['module', 'permissions'])
            ->when($request->filled('module'), fn ($q) => $q->where('module_id', $request->module))
            ->orderBy('module_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.roles.index', compact('roles', 'modules'));
    }

    public function create()
    {
        return view('admin.roles.create', [
            'modules' => Module::orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function show(Role $role)
    {
        $role->load(['module', 'permissions', 'users']);

        return view('admin.roles.show', compact('role'));
    }

    public function store(Request $request)
    {
        $request->merge(['slug' => Str::slug($request->name)]);

        $data = $request->validate([
            'module_id' => ['required', 'exists:modules,id'],
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                Rule::unique('roles', 'slug')->where(fn ($q) => $q->where('module_id', $request->module_id)),
            ],
            'description' => ['nullable', 'string'],
            'is_admin_role' => ['sometimes', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ], [
            'slug.unique' => 'A role with this name already exists in that module.',
        ]);

        $role = Role::create([
            'module_id' => $data['module_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'is_admin_role' => $request->boolean('is_admin_role'),
            'is_system' => false,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => Permission::orderBy('name')->get(),
            'assignedPermissionIds' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->merge(['slug' => Str::slug($request->name)]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                Rule::unique('roles', 'slug')
                    ->where(fn ($q) => $q->where('module_id', $role->module_id))
                    ->ignore($role->id),
            ],
            'description' => ['nullable', 'string'],
            'is_admin_role' => ['sometimes', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ], [
            'slug.unique' => 'A role with this name already exists in that module.',
        ]);

        $role->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'is_admin_role' => $request->boolean('is_admin_role'),
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $activeUserCount = $role->users()->count();

        if ($activeUserCount > 0) {
            return back()->with(
                'error',
                "Cannot delete \"{$role->name}\" — {$activeUserCount} user(s) currently hold this role. Reassign them first."
            );
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }
}