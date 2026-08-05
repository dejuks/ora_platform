<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBranch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Library Manager (manage-settings): full CRUD over the physical
 * locations the Library operates at — e.g. Jimma, Adama, Finfinnee,
 * Shashamane, Bale Robe, Nekemte — plus which Catalogers/Inventory
 * Managers/Physical Librarians/Acquisition Officers are scoped to
 * each one. See User::canAccessLibraryBranch() for what "scoped"
 * means in practice.
 */
class BranchController extends Controller
{
    /**
     * Roles whose permissions are branch-relevant — the ones whose
     * day-to-day work (cataloging, tagging, circulation, procurement)
     * actually happens at a physical location. Library Manager itself
     * is deliberately excluded: that role already bypasses branch
     * scoping entirely (see User::canAccessLibraryBranch()).
     */
    protected const BRANCH_SCOPED_ROLE_SLUGS = [
        'cataloger',
        'inventory-manager',
        'librarian-physical',
        'acquisition-officer',
    ];

    public function index()
    {
        $this->authorizeSettings();

        $branches = LibraryBranch::withCount(['copies', 'staff'])
            ->orderBy('name')
            ->paginate(20);

        return view('modules.library.branches.index', compact('branches'));
    }

    public function create()
    {
        $this->authorizeSettings();

        return view('modules.library.branches.create');
    }

    public function store(Request $request)
    {
        $this->authorizeSettings();

        $data = $this->validated($request);

        $data['code'] = LibraryBranch::uniqueCode($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = Auth::id();

        $branch = LibraryBranch::create($data);

        return redirect()
            ->route('library.branches.edit', $branch)
            ->with('success', 'Branch created. You can now assign staff to it below.');
    }

    public function edit(LibraryBranch $branch)
    {
        $this->authorizeSettings();

        $branch->load('staff');

        $staffPool = $this->branchScopedStaffPool();

        return view('modules.library.branches.edit', compact('branch', 'staffPool'));
    }

    public function update(Request $request, LibraryBranch $branch)
    {
        $this->authorizeSettings();

        $data = $this->validated($request, $branch->id);

        if ($data['name'] !== $branch->name) {
            $data['code'] = LibraryBranch::uniqueCode($data['name'], $branch->id);
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['updated_by'] = Auth::id();

        $branch->update($data);

        return redirect()
            ->route('library.branches.index')
            ->with('success', 'Branch updated.');
    }

    /**
     * Restricted the same way pricing plans are: a branch still
     * holding physical copies can't be deleted out from under them
     * (see the FK's restrictOnDelete) — the Inventory Manager has to
     * transfer or withdraw those copies first.
     */
    public function destroy(LibraryBranch $branch)
    {
        $this->authorizeSettings();

        if ($branch->copies()->exists()) {
            return back()->with('error', 'This branch still has physical copies assigned to it. Transfer or withdraw them first.');
        }

        $branch->delete();

        return redirect()
            ->route('library.branches.index')
            ->with('success', 'Branch deleted.');
    }

    /**
     * Replace this branch's full staff assignment list in one go —
     * simpler for the Library Manager than add/remove one at a time,
     * and matches how the edit form's checklist submits.
     */
    public function syncStaff(Request $request, LibraryBranch $branch)
    {
        $this->authorizeSettings();

        $data = $request->validate([
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $branch->staff()->sync(
            collect($data['user_ids'] ?? [])->mapWithKeys(fn ($id) => [$id => ['assigned_by' => Auth::id()]])
        );

        return back()->with('success', 'Staff assignments for this branch were updated.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:library_branches,name'.($ignoreId ? ",{$ignoreId}" : '')],
            'city' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return $data;
    }

    /**
     * Every user currently holding a branch-scoped Library role, for
     * the assignment checklist — pulled by role slug rather than a
     * flat permission check since several of those slugs share
     * permissions with each other (and Library Manager) but only
     * these four are meant to be branch-restrictable.
     */
    protected function branchScopedStaffPool()
    {
        $roleIds = Role::whereIn('slug', self::BRANCH_SCOPED_ROLE_SLUGS)
            ->whereHas('module', fn ($q) => $q->where('code', 'library'))
            ->pluck('id');

        return User::whereHas('moduleRoles', fn ($q) => $q->whereIn('roles.id', $roleIds))
            ->orderBy('first_name')
            ->get();
    }

    protected function authorizeSettings(): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', 'manage-settings'),
            403,
            'You do not have permission to do this.'
        );
    }
}
