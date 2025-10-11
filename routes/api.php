<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ProfilePhotoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Enjoy building your API!
|
*/

// Device monitoring API routes
Route::prefix('devices')->group(function () {
    Route::get('/', [DeviceController::class, 'index'])->name('api.devices.index');
    Route::get('/{id}', [DeviceController::class, 'show'])->name('api.devices.show');
    Route::post('/{id}/status', [DeviceController::class, 'recordStatus'])->name('api.devices.recordStatus');
    Route::post('/{id}/ping', [DeviceController::class, 'pingDevice'])->name('api.devices.ping');
});

// Profile photo API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile/photo', [ProfilePhotoController::class, 'show'])->name('api.profile.photo.show');
    Route::post('/profile/photo', [ProfilePhotoController::class, 'store'])->name('api.profile.photo.store');
    Route::delete('/profile/photo', [ProfilePhotoController::class, 'destroy'])->name('api.profile.photo.destroy');
});