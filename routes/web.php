<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
//    $migrationsPath = database_path('migrations/tenant');

//    if (File::exists($migrationsPath)) {
//        // Recursively get all migration files from the specific folder and its subfolders
//        $directories = File::directories($migrationsPath);
//
//        foreach ($directories as $directory) {
//           echo $directory . PHP_EOL;
//        }
//
//    }
    return view('welcome');
});
