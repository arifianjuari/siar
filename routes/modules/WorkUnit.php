<?php

use App\Http\Controllers\Modules\WorkUnitController;
use App\Http\Controllers\Modules\WorkUnitFixController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'tenant', 'check.permission:work-units,can_view'])
    ->prefix('work-units')
    ->name('work-units.')
    ->group(function () {
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

        // Route untuk dashboard unit kerja
        Route::get('/{workUnit}/dashboard', [WorkUnitController::class, 'dashboard'])
            ->middleware('check.permission:work-units,can_view') // Pastikan permission sesuai
            ->name('dashboard');
    });

// Rute perbaikan tanpa middleware permission untuk debugging
Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('work-units-fix')
    ->name('work-units.fix.')
    ->group(function () {
        // Daftar unit kerja (perbaikan)
        Route::get('/', [WorkUnitFixController::class, 'index'])->name('index');

        // Dashboard unit kerja (perbaikan)
        Route::get('/{workUnit}/dashboard', [WorkUnitFixController::class, 'dashboard'])->name('dashboard');
    });
