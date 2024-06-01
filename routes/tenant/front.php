<?php

use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Tenant $tenantInstance) {
    return [
        'welcome' => "Welcome Tenant (" . request()->tenant . ") Front Apis",
        'tenant' => $tenantInstance,
    ];
});
