<?php

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->validateCsrfTokens(except: [
        //     'api/*', // <-- Exclude your API route
        // ]);
        // $middleware->verifyCsrfTokens([
        //     'api/*'
        // ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user' => \App\Http\Middleware\UserMiddleware::class,
            'driver' => \App\Http\Middleware\DriverMiddleware::class,
            'shop_owner' => \App\Http\Middleware\ShopOwnerMiddleware::class,
            'recaptcha' => \App\Http\Middleware\VerifyRecaptcha::class,
        ]);

        $middleware->api([
            \Illuminate\Session\Middleware\StartSession::class,
            HandleCors::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (CustomException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getCode());
            }
        });
        // $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'error' => $e->getMessage(),
        //         ], $e->getCode());
        //     }
    
        //     return $request->expectsJson();
        // });
    })->create();
