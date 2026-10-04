<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleAuthorization::class,
            'pending' => \App\Http\Middleware\PendingRedirect::class,
            'suspended' => \App\Http\Middleware\EnforceSuspension::class,
            'verify' => \App\Http\Middleware\VerifyUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return redirect()->guest(route('login'))->with('error', __('messages.http.login_required'));
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return redirect()->back(fallback: route('welcome'))->with('error', __('messages.http.record_missing'));
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 403 || $request->expectsJson()) {
                return null;
            }

            return redirect()->back(fallback: route('welcome'))->with('error', __('messages.http.forbidden'));
        });
    })->create();
