<?php

namespace App\Services;

use App\Models\LibraryMember;
use App\Models\Module;
use App\Models\ResearcherProfile;
use App\Models\User;
use App\Models\UserModuleRole;
use Illuminate\Support\Str;

/**
 * Single source of truth for "grant this user the entry-level role of
 * this module, right now, no approval needed." Used by:
 *
 *  - RegisterController (public sign-up, one or more modules at once)
 *  - ModuleEnrollmentController (logged-in "My Modules" self-service page)
 *
 * Keeping this in one place means Phase 2+ never has six copies of
 * "look up the module, look up its default role, create the pivot row."
 */
class ModuleEnrollmentService
{
    /**
     * Enroll a user into a module's default (self-registration) role.
     *
     * Returns the Module on success, or null if the module doesn't
     * exist, isn't active, isn't self-registerable, or has no
     * default_role_slug configured — callers should treat null as
     * "silently skip," same as the original per-module enrollers did.
     */
    public function enroll(User $user, string $moduleCode): ?Module
    {
        $module = Module::selfRegisterable()
            ->where('code', $moduleCode)
            ->first();

        if (! $module) {
            return null;
        }

        $role = $module->defaultRole();

        if (! $role) {
            return null;
        }

        // updateOrCreate, not firstOrCreate: someone who left this
        // module before (is_active = false) and is now rejoining must
        // actually be reactivated. firstOrCreate would find that old
        // inactive row on the (user_id, module_id, role_id) match and
        // hand it back unchanged, leaving them enrolled-but-invisible
        // everywhere is_active is checked.
        UserModuleRole::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
                'role_id' => $role->id,
            ],
            [
                'is_active' => true,
            ]
        );

        $this->afterEnroll($user, $module);

        return $module;
    }

    /**
     * Self-service leave: revoke the entry-level role a user picked
     * up through enroll()/enrollMany() above, for a module they're
     * still permitted to leave (see canLeave()).
     *
     * Deliberately deactivates only that one default-role pivot row —
     * never every role row the user holds in the module. If a Super
     * Admin has since promoted them (Associate Editor, Journal
     * Manager, etc.), that's a separate, admin-granted role and this
     * self-service action must never quietly strip it.
     *
     * Returns true if an active enrollment was actually revoked,
     * false if there was nothing to leave or leaving isn't allowed.
     */
    public function leave(User $user, string $moduleCode): bool
    {
        $module = Module::where('code', $moduleCode)->first();

        if (! $module || ! $this->canLeave($user, $module)) {
            return false;
        }

        $role = $module->defaultRole();

        $updated = UserModuleRole::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where('role_id', $role->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return $updated > 0;
    }

    /**
     * A user may only leave a module through this self-service page
     * if the *only* active role they hold there is the module's own
     * default (self-registration) role. The moment an admin has
     * granted them anything else in that module — Associate Editor,
     * Journal Manager, Sysop, whatever — leaving stops being a
     * one-click self-service action, since it would also mean giving
     * up a role they didn't grant themselves.
     */
    public function canLeave(User $user, Module $module): bool
    {
        $defaultRole = $module->defaultRole();

        if (! $defaultRole) {
            return false;
        }

        $activeRoleIds = UserModuleRole::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where('is_active', true)
            ->pluck('role_id');

        return $activeRoleIds->isNotEmpty()
            && $activeRoleIds->every(fn ($roleId) => $roleId === $defaultRole->id);
    }

    /**
     * Some modules need more than the role pivot row to be usable —
     * Researcher Network expects a profile record to fill in, Library
     * expects a membership record to borrow against. Kept here, one
     * switch, instead of scattered per-module enrollment controllers.
     */
    protected function afterEnroll(User $user, Module $module): void
    {
        match ($module->code) {
            'researcher' => $user->researcherProfile()->firstOrCreate([]),
            'library' => $user->libraryMember()->firstOrCreate([], [
                'membership_no' => 'LIB-' . Str::upper(Str::random(8)),
                'member_type' => 'external',
                'status' => 'active',
                'max_active_loans' => 3,
                'joined_at' => now(),
            ]),
            default => null,
        };
    }

    /**
     * Enroll into several modules at once (used by public registration,
     * where a user checks multiple boxes in one submit). Skips any
     * module code the user is already enrolled in or that isn't
     * self-registerable, and returns the modules actually joined.
     */
    public function enrollMany(User $user, array $moduleCodes): array
    {
        $joined = [];

        foreach ($moduleCodes as $moduleCode) {
            $module = $this->enroll($user, $moduleCode);

            if ($module) {
                $joined[] = $module;
            }
        }

        return $joined;
    }
}
