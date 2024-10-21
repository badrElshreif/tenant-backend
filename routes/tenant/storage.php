<?php

/*============================IMAGE ROUTES===========================================*/

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;


Route::get('/storage/uploads/{width}x{height}/{category}/{file}', function ($width, $height, $category, $file) {

    $disk = Storage::disk('public');

    if ($disk->exists("uploads/$category/$file")) {
        $image = Image::read($disk->path("uploads/{$category}/$file"))->resize($width, $height);
        if (!$disk->exists("uploads/{$width}x{$height}/$category"))
            $disk->makeDirectory("uploads/{$width}x{$height}/$category");
        $image->save($disk->path("uploads/{$width}x{$height}/$category/$file"));
        return $image->response();
    }
    return Image::make($disk->path('uploads/default.png'))->resize($width, $height)->response();
});


Route::get('/storage/{category}/{file}', function ($category, $file) {
    //return 'gggg';
    $disk = Storage::disk('public');
    //return $file;
    if ($disk->exists("$category/$file")) {
        $path = $disk->path("$category/$file");
        return response()->download($path);
    }
    return null;
})->name('original');


Route::get('/storage/uploads/{width}X{height}/{dir}/{file}', function ($width, $height, $dir, $file) {
    $disk = Storage::disk('public');
    $tenantDir = getTenant()->slug;
    $dir = $tenantDir . '/' . $dir;
    if ($disk->exists("$dir/$file")) {
        if (!$disk->exists("$dir/{$width}x{$height}")) {
            $disk->makeDirectory("$dir/{$width}x{$height}");
        }

        if (!$disk->exists("$dir/{$width}x{$height}/$file")) {
            $image = Image::read($disk->path("{$dir}/$file"))->resize($width, $height);
            $image->save($disk->path("$dir/{$width}x{$height}/$file"));
        }
        //Storage::disk('public')->url("$dir/{$width}x{$height}/$file")
        return response()->file($disk->path("$dir/{$width}x{$height}/$file"));
    } else {
        return asset("assets/images/default/default-logo.png");
    }
})->name('image.resize');
