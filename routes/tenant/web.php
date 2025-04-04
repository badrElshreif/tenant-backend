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

Route::patch('brands/{id}/toggle-status', [\App\Tenant\Brand\Controllers\BrandController::class, 'toggleStatus'])
    ->name('brands.toggle-status');
Route::resource('/dashboard/brands', \App\Tenant\Brand\Controllers\BrandController::class);

// Show brand routes using Artisan command
Route::get('/routes', function() {
    // Get all routes directly from the router
    $routes = collect(Route::getRoutes())
  /*  ->filter(function ($route) {
        // Filter routes that contain 'brands' in their URI or name
        return stripos($route->uri(), 'brands') !== false ||
               ($route->getName() && stripos($route->getName(), 'brands') !== false);
    }) */
    ->map(function ($route) {
        // Format each route with complete information
        return [
            'method' => implode('|', $route->methods()),
            'uri' => '{tenant}.localhost/' . $route->uri(),
            'name' => $route->getName() ?: 'unnamed',
            'action' => $route->getActionName(),
            'middleware' => implode(', ', $route->gatherMiddleware())
        ];
    })->values();

    // Create a formatted HTML table for better readability
    $html = '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
    $html .= '<tr style="background-color: #f2f2f2;"><th>Method</th><th>URI</th><th>Name</th><th>Action</th><th>Middleware</th></tr>';

    foreach ($routes as $route) {
        $html .= '<tr>';
        $html .= '<td>' . $route['method'] . '</td>';
        $html .= '<td>' . $route['uri'] . '</td>';
        $html .= '<td>' . $route['name'] . '</td>';
        $html .= '<td>' . $route['action'] . '</td>';
        $html .= '<td>' . $route['middleware'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    return response()->make($html, 200, [
        'Content-Type' => 'text/html'
    ]);
});
