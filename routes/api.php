<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\NetworkMetricsController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;

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

// Network monitoring routes for Python agent
Route::get('/devices', [DeviceController::class, 'index']);
Route::post('/device/status', [DeviceController::class, 'updateStatus']);
Route::post('/device/scan', [DeviceController::class, 'scanDevice']);
Route::post('/device/batch-status', [DeviceController::class, 'batchUpdateStatus']);

// Network metrics and historical data
Route::get('/metrics/network', [NetworkMetricsController::class, 'getNetworkMetrics']);
Route::get('/metrics/device/{device}', [NetworkMetricsController::class, 'getDeviceMetrics']);

// Alerts management
Route::get('/alerts', [AlertController::class, 'index']);
Route::put('/alerts/{alert}/resolve', [AlertController::class, 'resolve']);
Route::get('/alerts/unresolved', [AlertController::class, 'getUnresolved']);

// Reports
Route::get('/reports/overview', [ReportController::class, 'getOverview']);
Route::post('/reports/generate', [ReportController::class, 'generate']);

    // Real-time data endpoint for dashboard charts
    Route::get('/metrics/realtime', [NetworkMetricsController::class, 'getRealTimeResponseTimeData']);

// Additional device management routes
Route::post('/devices/bulk-ping', [DeviceController::class, 'bulkPing']);
Route::get('/devices/hierarchy', [DeviceController::class, 'getHierarchy']);
Route::get('/devices/hierarchy/realtime', [DeviceController::class, 'getRealTimeHierarchy']);
Route::get('/devices/{id}/children', [DeviceController::class, 'getChildren']);
Route::get('/devices/{id}/family', [DeviceController::class, 'getFamily']);

// User management API routes (Admin only)
Route::middleware(['auth'])->group(function () {
    Route::apiResource('users', UserController::class)->names('api.users');
    
    // User photo management routes
    Route::prefix('users')->group(function () {
        Route::post('/{user}/photo', [UserController::class, 'updatePhoto'])->name('api.users.photo.update');
        Route::delete('/{user}/photo', [UserController::class, 'removePhoto'])->name('api.users.photo.remove');
    });
});

// Note: Profile photo routes moved to web.php to support session authentication