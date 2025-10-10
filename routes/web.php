<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ReportController;
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
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
