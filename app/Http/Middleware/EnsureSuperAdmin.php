<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Allow the request through only if the authenticated user is a
     * Super Admin. Super Admin is the single role that manages every
     * module, every user, and the module list itself.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isSuperAdmin()) {
            abort(403, 'This area is restricted to Super Admins.');
        }

        return $next($request);
    }
}
