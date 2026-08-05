<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\ModuleEnrollmentService;
use Illuminate\Support\Facades\Auth;

/**
 * "My Modules" — lets an already-registered user see what they're
 * enrolled in and join additional self-registerable modules later,
 * without going through Super Admin. Reuses ModuleEnrollmentService
 * so the enrollment logic matches public registration exactly.
 */
class ModuleEnrollmentController extends Controller
{
    public function __construct(protected ModuleEnrollmentService $enrollment)
    {
    }

    public function index()
    {
        $user = Auth::user();

        $modules = Module::selfRegisterable()->orderBy('name')->get();

        $joinedModuleIds = $user->moduleRoles()
            ->get()
            ->pluck('pivot.module_id')
            ->unique();

        $joined = $modules->whereIn('id', $joinedModuleIds)
            ->each(function (Module $module) use ($user) {
                // Only offer "Leave" where it's actually safe to act on
                // with one click — see ModuleEnrollmentService::canLeave().
                $module->can_leave = $this->enrollment->canLeave($user, $module);
            });

        $available = $modules->whereNotIn('id', $joinedModuleIds);

        return view('modules.my-modules', compact('joined', 'available'));
    }

    public function join(string $moduleCode)
    {
        $user = Auth::user();

        $module = $this->enrollment->enroll($user, $moduleCode);

        if (! $module) {
            return redirect()
                ->route('my-modules')
                ->with('error', 'That module is not available to join.');
        }

        return redirect()
            ->route('my-modules')
            ->with('success', "You're now enrolled in {$module->name}.");
    }

    public function leave(string $moduleCode)
    {
        $user = Auth::user();

        $module = Module::where('code', $moduleCode)->first();

        if (! $module) {
            return redirect()->route('my-modules')->with('error', 'That module does not exist.');
        }

        if (! $this->enrollment->leave($user, $moduleCode)) {
            return redirect()
                ->route('my-modules')
                ->with('error', "You can't leave {$module->name} here — an administrator has granted you a role there beyond the default one. Contact an administrator if you need it removed.");
        }

        return redirect()
            ->route('my-modules')
            ->with('success', "You've left {$module->name}.");
    }
}
