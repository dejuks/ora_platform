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

        $joined = $modules->whereIn('id', $joinedModuleIds);
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
}
