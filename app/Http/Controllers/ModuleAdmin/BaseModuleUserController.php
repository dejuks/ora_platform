<?php

namespace App\Http\Controllers\ModuleAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Shared logic behind every module's own "Manage Users" screen.
 *
 * Each module (Journal, Ebook, Library, Researcher, Wiki, Repository)
 * has a thin controller that extends this and sets $moduleCode. A
 * module admin using this screen can only ever view, add, edit, or
 * remove users inside THEIR module, and can assign any role that
 * belongs to that module — including its top-level admin role
 * (e.g. Journal Manager, Library Manager).
 *
 *  - "Remove" detaches this user's roles in THIS module only. Roles
 *    they hold in other modules, and the account itself, are untouched.
 */
abstract class BaseModuleUserController extends Controller
{
    protected string $moduleCode;

    protected function module(): Module
    {
        return Module::where('code', $this->moduleCode)->firstOrFail();
    }

    /**
     * Roles a module admin is allowed to hand out from this screen —
     * every role that belongs to this module, admin-type roles
     * included (e.g. Library Manager, Journal Manager).
     */
    protected function assignableRoles(Module $module)
    {
        return Role::where('module_id', $module->id)
            ->orderBy('name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Extension hooks
    |--------------------------------------------------------------------------
    |
    | No-op by default. A module whose "Manage Users" screen needs an
    | extra field beyond the generic account/role fields (e.g. Library
    | branch assignment) overrides these instead of re-implementing
    | the whole CRUD flow.
    |
    */

    /**
     * Extra validation rules merged into store()/update().
     */
    protected function extraValidationRules(): array
    {
        return [];
    }

    /**
     * Relations to eager-load on the index() listing (e.g.
     * 'libraryBranches') beyond the module roles already loaded.
     */
    protected function extraIndexRelations(): array
    {
        return [];
    }

    /**
     * Extra view data merged into create()/edit().
     */
    protected function extraFormData(?User $user = null): array
    {
        return [];
    }

    /**
     * Extra view data merged into show().
     */
    protected function extraShowData(User $user): array
    {
        return [];
    }

    /**
     * Called after the user record and module roles are saved, inside
     * the same DB transaction, with the validated request data
     * (module-specific keys included).
     */
    protected function afterUserSaved(User $user, array $data): void
    {
        //
    }

    /**
     * List every distinct user holding at least one role in this
     * module, along with which role(s) they hold here.
     */
    public function index(Request $request)
    {
        $module = $this->module();

        $users = User::whereHas('moduleRoles', function ($query) use ($module) {
            $query->where('roles.module_id', $module->id);
        })
            ->with(['moduleRoles' => function ($query) use ($module) {
                $query->where('roles.module_id', $module->id);
            }])
            ->when($this->extraIndexRelations(), fn ($query, $relations) => $query->with($relations))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('module-admin.users.index', [
            'module' => $module,
            'moduleCode' => $this->moduleCode,
            'users' => $users,
        ]);
    }

    public function create()
    {
        $module = $this->module();

        return view('module-admin.users.create', array_merge([
            'module' => $module,
            'moduleCode' => $this->moduleCode,
            'roles' => $this->assignableRoles($module),
        ], $this->extraFormData()));
    }

    public function store(Request $request)
    {
        $module = $this->module();

        $data = $request->validate(array_merge([
            'employee_no' => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'in:Male,Female'],
            'date_of_birth' => ['nullable', 'date'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'in:Active,Inactive'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer'],
        ], $this->extraValidationRules()));

        $roles = $this->assignableRoles($module)->whereIn('id', $data['roles']);

        abort_if($roles->isEmpty(), 422, 'Select at least one valid role.');

        DB::transaction(function () use ($data, $module, $roles) {

            $userData = $data;
            $userData['password'] = Hash::make($userData['password']);
            $userData['is_super_admin'] = false;
            $userData['created_by'] = auth()->id();

            unset($userData['roles']);
            $userData = array_diff_key($userData, array_flip(array_keys($this->extraValidationRules())));

            $user = User::create($userData);

            $sync = [];

            foreach ($roles as $role) {
                $sync[$role->id] = ['module_id' => $module->id, 'is_active' => true];
            }

            $user->moduleRoles()->attach($sync);

            $this->afterUserSaved($user, $data);
        });

        return redirect()
            ->route("{$this->moduleCode}.admin.users.index")
            ->with('success', 'User added to '.$module->name.'.');
    }

    public function show(User $user)
    {
        $module = $this->module();

        $this->authorizeBelongsToModule($user);

        return view('module-admin.users.show', array_merge([
            'module' => $module,
            'moduleCode' => $this->moduleCode,
            'user' => $user,
            'roles' => $user->rolesInModule($this->moduleCode),
        ], $this->extraShowData($user)));
    }

    public function edit(User $user)
    {
        $module = $this->module();

        $this->authorizeBelongsToModule($user);

        $assignedRoleIds = $user->rolesInModule($this->moduleCode)->pluck('id')->all();

        return view('module-admin.users.edit', array_merge([
            'module' => $module,
            'moduleCode' => $this->moduleCode,
            'user' => $user,
            'roles' => $this->assignableRoles($module),
            'assignedRoleIds' => $assignedRoleIds,
        ], $this->extraFormData($user)));
    }

    public function update(Request $request, User $user)
    {
        $module = $this->module();

        $this->authorizeBelongsToModule($user);

        $data = $request->validate(array_merge([
            'employee_no' => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'in:Male,Female'],
            'date_of_birth' => ['nullable', 'date'],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'in:Active,Inactive'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer'],
        ], $this->extraValidationRules()));

        $roles = $this->assignableRoles($module)->whereIn('id', $data['roles']);

        abort_if($roles->isEmpty(), 422, 'Select at least one valid role.');

        $userData = $data;

        if (! empty($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        } else {
            unset($userData['password']);
        }

        unset($userData['roles']);
        $userData = array_diff_key($userData, array_flip(array_keys($this->extraValidationRules())));

        $userData['updated_by'] = auth()->id();

        DB::transaction(function () use ($userData, $data, $user, $module, $roles) {

            $user->update($userData);

            $user->moduleRoles()->detach($module->roles()->pluck('id'));

            $sync = [];

            foreach ($roles as $role) {
                $sync[$role->id] = ['module_id' => $module->id, 'is_active' => true];
            }

            $user->moduleRoles()->attach($sync);

            $this->afterUserSaved($user, $data);
        });

        return redirect()
            ->route("{$this->moduleCode}.admin.users.index")
            ->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $module = $this->module();

        $this->authorizeBelongsToModule($user);

        $user->moduleRoles()->detach($module->roles()->pluck('id'));

        return redirect()
            ->route("{$this->moduleCode}.admin.users.index")
            ->with('success', 'User removed from '.$module->name.'.');
    }

    protected function authorizeBelongsToModule(User $user): void
    {
        abort_if($user->isSuperAdmin(), 403, 'Super Admin accounts cannot be managed from a module panel.');

        abort_unless(
            $user->hasModuleAccess($this->moduleCode),
            404
        );
    }
}
