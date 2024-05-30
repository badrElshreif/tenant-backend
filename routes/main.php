<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return ['Welcome Main'];
});

Route::post('tenant/create', \App\Main\Tenant\Actions\CreateTenantAction::class);
