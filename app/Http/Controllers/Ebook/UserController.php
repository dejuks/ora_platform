<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\ModuleAdmin\BaseModuleUserController;
use App\Models\Module;
use App\Models\Role;
class UserController extends BaseModuleUserController
{
    protected string $moduleCode = 'ebook';
    protected function assignableRoles(Module $module)
    {
        return Role::where('module_id', $module->id)
            ->orderBy('name')
            ->get();
    }
}
