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

        UserModuleRole::firstOrCreate(
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
