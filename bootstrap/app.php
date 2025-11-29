<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

use App\Http\Middleware\SyncKeycloakUser;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            SyncKeycloakUser::class,
        ]);
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        $middleware->priority([
            ForceJsonResponse::class,
            Authenticate::class,
            SyncKeycloakUser::class,
            CheckRole::class,
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $e) {
            return $request->is('api/*');
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });
    })->create();
