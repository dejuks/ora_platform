<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('moduleRoles.module')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $modules = Module::where('is_active', true)
            ->orderBy('name')
            ->with('roles')
            ->get();

        return view('admin.users.create', compact('modules'));
    }

    public function store(StoreUserRequest $request)
    {
        DB::transaction(function () use ($request) {

            $data = $request->validated();

            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('users', 'public');
            }

            $data['password'] = Hash::make($data['password']);
            $data['is_super_admin'] = $request->boolean('is_super_admin');
            $data['created_by'] = auth()->id();

            $user = User::create($data);

            // Each role already knows which module it belongs to — one
            // flat "roles" array covers access across every module.
            $this->syncRoles($user, $data['roles'] ?? []);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('moduleRoles.module', 'moduleRoles.permissions');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $modules = Module::where('is_active', true)
            ->orderBy('name')
            ->with('roles')
            ->get();

        $user->load('moduleRoles');

        return view('admin.users.edit', compact('user', 'modules'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if ($user->isSuperAdmin() && ! $request->boolean('is_super_admin')) {

            $remainingSuperAdmins = User::where('is_super_admin', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($remainingSuperAdmins === 0) {
                return back()
                    ->withInput()
                    ->with('error', 'At least one Super Admin must remain in the system.');
            }
        }

        DB::transaction(function () use ($request, $user) {

            $data = $request->validated();

            if ($request->hasFile('profile_photo')) {

                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $data['profile_photo'] = $request->file('profile_photo')->store('users', 'public');
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $data['is_super_admin'] = $request->boolean('is_super_admin');
            $data['updated_by'] = auth()->id();

            $user->update($data);

            $this->syncRoles($user, $data['roles'] ?? []);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id == auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {

            $remainingSuperAdmins = User::where('is_super_admin', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($remainingSuperAdmins === 0) {
                return back()->with('error', 'At least one Super Admin must remain in the system.');
            }
        }

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->moduleRoles()->detach();

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $status = match ($user->status) {
            'Active' => 'Inactive',
            'Inactive' => 'Active',
            default => 'Active',
        };

        $user->update([
            'status' => $status,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'User status updated successfully.');
    }

    /**
     * Sync a user's role assignments. $roleIds can span any number of
     * modules and include more than one role from the same module
     * (e.g. Author + Reviewer in Journal Management). This is what
     * actually writes into user_module_roles.
     */
    protected function syncRoles(User $user, array $roleIds): void
    {
        $roles = Role::whereIn('id', $roleIds)->get();

        $sync = [];

        foreach ($roles as $role) {
            $sync[$role->id] = [
                'module_id' => $role->module_id,
                'is_active' => true,
            ];
        }

        $user->moduleRoles()->sync($sync);
    }
}