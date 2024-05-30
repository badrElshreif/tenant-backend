<?php

use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Tenant $tenantInstance) {
    //echo ($tenantObj->name);
    return [
        'welcome' => "Welcome Tenant (" . request()->tenant . ") Front Apis",
        'tenant' => $tenantInstance,
    ];
});
