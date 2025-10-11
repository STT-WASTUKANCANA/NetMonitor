<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

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