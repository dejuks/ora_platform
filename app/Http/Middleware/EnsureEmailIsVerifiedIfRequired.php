<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

/**
 * Drop-in replacement for Laravel's built-in 'verified' middleware
 * (registered under the same alias in bootstrap/app.php) — identical
 * behavior, except it only actually enforces the check while a Super
 * Admin has "Require email verification" turned on in Settings.
 *
 * Turning the setting off doesn't touch anyone's verified status —
 * it just stops gating access on it. Turn it back on and everyone
 * who never verified is immediately gated again, no re-migration
 * needed.
 */
class EnsureEmailIsVerifiedIfRequired
{
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        if (! SystemSetting::current()->require_email_verification) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}
