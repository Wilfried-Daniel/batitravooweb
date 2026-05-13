<?php

use App\Http\Middleware\EnsureAppProfileSlug;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureNotAdmin;
use App\Http\Middleware\EnsureUserIsApiActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureIsAdmin::class,
            'api.active' => EnsureUserIsApiActive::class,
            'not_admin' => EnsureNotAdmin::class,
            'app.profile' => EnsureAppProfileSlug::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
