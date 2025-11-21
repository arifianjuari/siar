<?php

use Modules\WorkUnit\Http\Controllers\WorkUnitController;
use Modules\WorkUnit\Http\Controllers\WorkUnitFixController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Work Unit Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Work Unit module.
| These routes use the 'web', 'auth', 'tenant' middleware and permission checks.
|
*/

// Main Work Unit Routes
Route::middleware(['web', 'auth', 'tenant', 'check.permission:work-units,can_view'])
    ->prefix('work-units')
    ->name('modules.work-units.')
    ->group(function () {
        
        // Work Unit CRUD
        Route::get('/', [WorkUnitController::class, 'index'])->name('index');

        Route::middleware('check.permission:work-units,can_create')
            ->group(function () {
                Route::get('/create', [WorkUnitController::class, 'create'])->name('create');
                Route::post('/', [WorkUnitController::class, 'store'])->name('store');
            });

        Route::middleware('check.permission:work-units,can_edit')
            ->group(function () {
                Route::get('/{workUnit}/edit', [WorkUnitController::class, 'edit'])->name('edit');
                Route::put('/{workUnit}', [WorkUnitController::class, 'update'])->name('update');
            });

        Route::middleware('check.permission:work-units,can_delete')
            ->group(function () {
                Route::delete('/{workUnit}', [WorkUnitController::class, 'destroy'])->name('destroy');
            });

        // Work Unit Dashboard
        Route::get('/{workUnit}/dashboard', [WorkUnitController::class, 'dashboard'])
            ->middleware('check.permission:work-units,can_view')
            ->name('dashboard');

        // Global Dashboard
        Route::get('/global-dashboard', [WorkUnitController::class, 'globalDashboard'])
            ->middleware('check.permission:work-units,can_view')
            ->name('global-dashboard');

        // Note: SPO routes have been moved to SPOManagement module
        // See modules/SPOManagement/Http/routes.php
    });

// Fix Routes (for debugging - without strict permissions)
Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('work-units-fix')
    ->name('modules.work-units.fix.')
    ->group(function () {
        Route::get('/', [WorkUnitFixController::class, 'index'])->name('index');
        Route::get('/{workUnit}/dashboard', [WorkUnitFixController::class, 'dashboard'])->name('dashboard');
    });
