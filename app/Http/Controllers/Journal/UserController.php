<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\ModuleAdmin\BaseModuleUserController;

/**
 * Scoped user management for the Journal module admin.
 * All the actual logic lives in BaseModuleUserController.
 */
class UserController extends BaseModuleUserController
{
    protected string $moduleCode = 'journal';
}