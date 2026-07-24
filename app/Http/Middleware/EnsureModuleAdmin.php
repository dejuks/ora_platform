<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAdmin
{
    /**
     * Allow the request through only if the user is the admin of the
     * given module (is_admin = true on their user_modules row), or is
     * a Super Admin. This is what makes each module have "its own
     * admin" while Super Admin still oversees everything.
     *
     * Usage in routes: ->middleware('module.admin:journal')
     */
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isModuleAdmin($moduleCode)) {
            abort(403, 'This area is restricted to this module\'s admin.');
        }

        return $next($request);
    }
}
