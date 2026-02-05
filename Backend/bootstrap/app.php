<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ModelNotFoundException $e, Request $request): Response {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Recurso no encontrado.'], 404);
            }

            return redirect()->back()->withErrors(['general' => 'El recurso solicitado no fue encontrado.']);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request): ?Response {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            return null;
        });
    })->create();
