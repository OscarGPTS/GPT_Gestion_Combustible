<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelRecordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/login/google', [LoginController::class, 'redirectToProvider'])->name('login.redirect');
Route::get('/auth/google/callback', [LoginController::class, 'handleProviderCallback'])->name('login.callback');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');

    // Vehicles
    Route::resource('vehicles', VehicleController::class);

    // Fuel records
    Route::resource('fuel-records', FuelRecordController::class)->except(['create', 'edit']);

    // Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
