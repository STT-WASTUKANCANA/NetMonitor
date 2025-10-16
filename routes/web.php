<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/realtime', [DashboardController::class, 'getRealTimeData'])->middleware(['auth', 'verified'])->name('dashboard.realtime');

Route::middleware('auth')->group(function () {
    // Device routes
    Route::resource('devices', DeviceController::class);
    
    // Alert routes
    Route::resource('alerts', AlertController::class)->only(['index', 'update']);
    Route::put('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');
    
    // Report routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/response-time', [ReportController::class, 'getResponseTimeData'])->name('reports.response-time');
    Route::get('/reports/status', [ReportController::class, 'getStatusData'])->name('reports.status');
    Route::post('/reports/pdf', [ReportController::class, 'generatePdf'])->name('reports.pdf');
    
    // User management routes (Admin only)
    Route::resource('users', UserController::class);
    // Separate routes for user photo management
    Route::post('/users/{user}/photo', [UserController::class, 'updatePhoto'])->name('users.photo.update');
    Route::delete('/users/{user}/photo', [UserController::class, 'removePhoto'])->name('users.photo.remove');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    
    // Profile photo API-style routes for frontend JavaScript integration
    Route::prefix('api')->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('/profile/photo', [App\Http\Controllers\Api\ProfilePhotoController::class, 'show'])->name('api.profile.photo.show');
            Route::post('/profile/photo', [App\Http\Controllers\Api\ProfilePhotoController::class, 'store'])->name('api.profile.photo.store');
            Route::delete('/profile/photo', [App\Http\Controllers\Api\ProfilePhotoController::class, 'destroy'])->name('api.profile.photo.destroy');
            
            // User photo management for admin
            Route::prefix('users')->middleware('can:edit users')->group(function () {
                Route::post('/{user}/photo', [App\Http\Controllers\Api\UserController::class, 'updatePhoto'])->name('api.users.photo.update');
                Route::delete('/{user}/photo', [App\Http\Controllers\Api\UserController::class, 'removePhoto'])->name('api.users.photo.remove');
            });
        });
    });
    
    // Separate route for device ping to avoid conflict with API routes
    Route::post('/api/device/{id}/ping', [App\Http\Controllers\Api\DeviceController::class, 'pingDevice'])->middleware(['auth'])->name('api.device.ping');
});

require __DIR__.'/auth.php';