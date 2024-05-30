<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: [],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            if (empty(getSubdomain())) {
                Route::prefix('api')
                    ->middleware('api')
                    ->group(base_path('routes/api.php'));

                Route::prefix('api')
                    ->middleware('api')
                    ->group(base_path('routes/main.php'));

            } else {
                Route::middleware(['tenant-db-connection'])->group(function () {
                    Route::domain('{tenant}.' . env('APP_DOMAIN'))
                        ->prefix('api/dashboard')
                        ->name('tenant.dashboard.')
                        ->group(__DIR__ . '/../routes/tenant/dashboard.php');

                    Route::domain('{tenant}.' . env('APP_DOMAIN'))
                        ->middleware(['api'])
                        ->prefix('api')
                        ->name('tenant.')
                        ->group(__DIR__ . '/../routes/tenant/front.php');
                });
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tenant-db-connection' => \App\Infrastructure\Http\Middleware\TenantDatabaseConnection::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withCommands([
        \App\Infrastructure\Console\Commands\CreateTenant::class
    ])->create();
