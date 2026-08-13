<?php

use App\Http\Middleware\EnforceIdleTimeout;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserManager;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(
            except: ['appearance', 'sidebar_state'],
        );

        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'user-manager' => EnsureUserManager::class,
            'idle' => EnforceIdleTimeout::class,
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (app()->isProduction()) {
                error_log(
                    '[SKILLPATH_RENDER_EXCEPTION] '.json_encode(
                        [
                            'class' => $exception::class,
                            'message' => $exception->getMessage(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'method' => $request->method(),
                            'path' => $request->path(),
                        ],
                        JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE,
                    ),
                );
            }

            return null;
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
