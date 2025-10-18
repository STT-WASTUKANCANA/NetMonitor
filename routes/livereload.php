<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

// Route for Laravel live reload timestamp checker
Route::get('/_laravel_timestamp', function () {
    if (!app()->environment('local', 'development')) {
        abort(404);
    }
    
    $timestampFile = storage_path('framework/cache/bladewatch-timestamp');
    
    if (File::exists($timestampFile)) {
        $timestamp = File::get($timestampFile);
    } else {
        $timestamp = time();
    }
    
    return response($timestamp)->header('Content-Type', 'text/plain');
})->middleware('web');