<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Dashboard\DashboardController;
use App\Http\Controllers\Modules\Dashboard\PageController;

/*
|--------------------------------------------------------------------------
| Dashboard Module Routes
|--------------------------------------------------------------------------
|
| Routes untuk Dashboard dan halaman statis
|
*/

// Dashboard route
Route::middleware(['web', 'auth', 'tenant'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

// Static pages routes
Route::middleware(['web'])
    ->group(function () {
        Route::get('/help', [PageController::class, 'help'])->name('pages.help');
        Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
        Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
    });
