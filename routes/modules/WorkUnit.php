<?php

use App\Http\Controllers\Modules\WorkUnit\SPOController;
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

        // Route untuk SPO
        Route::prefix('spo')->name('spo.')->group(function () {
            Route::get('/', [SPOController::class, 'index'])->name('index');

            Route::middleware('check.permission:work-units,can_create')
                ->group(function () {
                    Route::get('/create', [SPOController::class, 'create'])->name('create');
                    Route::post('/', [SPOController::class, 'store'])->name('store');
                });

            Route::middleware('check.permission:work-units,can_view')
                ->group(function () {
                    Route::get('/{spo}', [SPOController::class, 'show'])->name('show');
                });

            Route::middleware('check.permission:work-units,can_edit')
                ->group(function () {
                    Route::get('/{spo}/edit', [SPOController::class, 'edit'])->name('edit');
                    Route::put('/{spo}', [SPOController::class, 'update'])->name('update');
                });

            Route::middleware('check.permission:work-units,can_delete')
                ->group(function () {
                    Route::delete('/{spo}', [SPOController::class, 'destroy'])->name('destroy');
                });

            // Route untuk generate PDF
            Route::get('/{spo}/generate-pdf', [SPOController::class, 'generatePdf'])->name('generate-pdf');

            // Route untuk generate QR code
            Route::get('/{spo}/qr-code', [SPOController::class, 'generateQr'])->name('qr-code');
        });
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
