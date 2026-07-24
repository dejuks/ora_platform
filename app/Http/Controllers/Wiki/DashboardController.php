<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;

/**
 * Placeholder dashboard for the Oromo Wikipedia module.
 *
 * This exists to prove the module-access / module-admin middleware and
 * routing work end to end. Real Oromo Wikipedia features (records, workflows,
 * CRUD, etc.) get built out inside this module folder next.
 */
class DashboardController extends Controller
{
    public function index()
    {
        return view('modules.wiki.dashboard', [
            'moduleLabel' => 'Oromo Wikipedia',
        ]);
    }

    public function admin()
    {
        return view('modules.wiki.admin-dashboard', [
            'moduleLabel' => 'Oromo Wikipedia',
        ]);
    }
}
