<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModulePermission
{
    /**
     * Allow the request through only if the user holds a role in the
     * given module that carries the given permission, or is Super Admin.
     *
     * Usage in routes:
     *   ->middleware('module.permission:journal,assign-reviewers')
     */
    public function handle(Request $request, Closure $next, string $moduleCode, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasModulePermission($moduleCode, $permission)) {
            abort(403, 'You do not have permission to do this.');
        }

        return $next($request);
    }
}