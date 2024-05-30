<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ["Welcome Tenant (" . request()->tenant . ") Dashboard Apis"];
});
