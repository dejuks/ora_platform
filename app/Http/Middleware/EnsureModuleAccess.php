<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    /**
     * Allow the request through only if the user has been granted access
     * to the given module (via user_modules), or is a Super Admin.
     *
     * Usage in routes: ->middleware('module.access:journal')
     */
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasModuleAccess($moduleCode)) {
            abort(403, 'You do not have access to this module.');
        }

        return $next($request);
    }
}
