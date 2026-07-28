<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\ModuleAdmin\BaseModuleUserController;
use App\Models\Module;
use App\Models\Role;

class UserController extends BaseModuleUserController
{
    protected string $moduleCode = 'ebook';

    /**
     * Unlike Journal's "Journal Manager" (assigned only from
     * Admin > Users), the Ebook module's own Manage Users screen is
     * also meant to be where "Book Editor" gets assigned — so unlike
     * the base behaviour, admin-type roles aren't excluded here.
     */
    protected function assignableRoles(Module $module)
    {
        return Role::where('module_id', $module->id)
            ->orderBy('name')
            ->get();
    }
}