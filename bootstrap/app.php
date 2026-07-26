<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'module.access' => \App\Http\Middleware\EnsureModuleAccess::class,
            'module.admin' => \App\Http\Middleware\EnsureModuleAdmin::class,
            'module.permission' => \App\Http\Middleware\EnsureModulePermission::class,
            'wiki.not_blocked' => \App\Http\Middleware\EnsureNotWikiBlocked::class,
        ]);

        // Chapa calls these routes server-to-server with no session and
        // no CSRF token — they must stay excluded.
        $middleware->validateCsrfTokens(except: [
            'journal/payments/chapa/webhook',
            'ebook/payments/chapa/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
