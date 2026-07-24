<?php

namespace App\Http\Controllers\Repository;

use App\Http\Controllers\ModuleAdmin\BaseModuleUserController;

/**
 * Scoped user management for the Repository module admin.
 * All the actual logic lives in BaseModuleUserController.
 */
class UserController extends BaseModuleUserController
{
    protected string $moduleCode = 'repository';
}
