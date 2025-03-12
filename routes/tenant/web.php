<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  //  return "Welcome Store";

    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => $route->gatherMiddleware(),
        ];
    });

    return response()->json($routes);
});


Route::get('/dashboard', [\App\Tenant\Brand\Controllers\BrandController::class, 'index'])->name("dashboard");

Route::resource('/dashboard/brands', \App\Tenant\Brand\Controllers\BrandController::class);
