<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\HandleInertiaRequests;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    // web: __DIR__ . '/../routes/web.php',
        api: [],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            if (!empty(getSubdomain()) || request()->headers->has('tenant') || getDomain() != env('APP_DOMAIN')) {
                //Tenants Routes
                $route = Route::middleware(['set-locale', 'tenant-db-connection', 'tenant-expire-token']);
                if (!request()->headers->has('tenant') && getDomain() == env('APP_DOMAIN')) {
                    $route->domain('{tenant}.' . env('APP_DOMAIN'));
                }

                $route->group(function () {

                    Route::prefix('/')
                        ->middleware(['web'])
                        ->name('tenant.dashboard.')
                        ->group(__DIR__ . '/../routes/tenant/web.php');

                    //Dashboard apis
                    Route::prefix('api/dashboard')
                        ->name('tenant.')
                        ->group(__DIR__ . '/../routes/tenant/dashboard.php');

                    //Front apis
                    Route::prefix('api')
                        ->name('tenant.')
                        ->group(__DIR__ . '/../routes/tenant/front.php');

                    Route::prefix('/')
                        ->name('tenant.')
                        ->group(__DIR__ . '/../routes/tenant/storage.php');
                });
            } else {

                Route::prefix('/')->group(base_path('routes/web.php'));

                Route::prefix('api')
                    ->middleware('api')
                    ->group(base_path('routes/api.php'));

                Route::prefix('api')
                    ->middleware('api')
                    ->group(base_path('routes/main.php'));

            }

        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'inertia-request' =>  HandleInertiaRequests::class,
            'tenant-db-connection' => \App\Infrastructure\Http\Middleware\TenantDatabaseConnection::class,
            'tenant-expire-token' => \App\Infrastructure\Http\Middleware\TenantExpireToken::class,
            'tenant-admin-type' => \App\Infrastructure\Http\Middleware\TenantAdminType::class,
            'set-locale' => \App\Infrastructure\Http\Middleware\SetLocale::class,
        ]);

        $middleware->appendToGroup('/',[
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        $middleware->appendToGroup('web', [
            Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->acceptsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => '404 not found.'
                ], 404);
            }
        });
    })->withCommands([
        \App\Infrastructure\Console\Commands\CreateTenant::class,
        \App\Infrastructure\Console\Commands\TenantPassport::class,
        \App\Infrastructure\Console\Commands\SeedTenant::class,
        \App\Infrastructure\Console\Commands\MigrateTenant::class,
    ])->create();



