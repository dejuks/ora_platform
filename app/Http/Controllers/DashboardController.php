<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Smart landing route. Every logged-in user hits /dashboard; this
     * is the single place that decides where they actually belong.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $moduleCode = $user->moduleRoles()
            ->with('module')
            ->get()
            ->pluck('module.code')
            ->unique()
            ->sort()
            ->first();

        if ($moduleCode) {
            return redirect()->route("{$moduleCode}.dashboard");
        }

        return view('dashboard.no-access');
    }

    /**
     * Super Admin control-panel dashboard.
     */
    public function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'Active')->count(),
            'inactive_users' => User::where('status', '!=', 'Active')->count(),
            'total_modules' => Module::count(),
            'active_modules' => Module::where('is_active', true)->count(),
            'super_admins' => User::where('is_super_admin', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
