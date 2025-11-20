<?php

use Illuminate\Support\Facades\Route;
use Modules\SPOManagement\Http\Controllers\SPOController;

/*
|--------------------------------------------------------------------------
| SPO Management Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the SPO Management module.
| These routes use the 'web', 'auth', 'tenant' middleware and permission checks.
|
*/

Route::middleware(['check.permission:spo-management,can_view'])
    ->prefix('spo')
    ->name('spo.')
    ->group(function () {
        
        // SPO List & Dashboard
        Route::get('/', [SPOController::class, 'index'])->name('index');
        Route::get('/dashboard', [SPOController::class, 'dashboard'])->name('dashboard');

        // View SPO
        Route::get('/{spo}', [SPOController::class, 'show'])->name('show');

        // Create SPO
        Route::middleware('check.permission:spo-management,can_create')
            ->group(function () {
                Route::get('/create', [SPOController::class, 'create'])->name('create');
                Route::post('/', [SPOController::class, 'store'])->name('store');
            });

        // Edit SPO
        Route::middleware('check.permission:spo-management,can_edit')
            ->group(function () {
                Route::get('/{spo}/edit', [SPOController::class, 'edit'])->name('edit');
                Route::put('/{spo}', [SPOController::class, 'update'])->name('update');
            });

        // Delete SPO
        Route::middleware('check.permission:spo-management,can_delete')
            ->group(function () {
                Route::delete('/{spo}', [SPOController::class, 'destroy'])->name('destroy');
            });

        // SPO Actions (PDF, QR Code)
        Route::get('/{spo}/generate-pdf', [SPOController::class, 'generatePdf'])->name('generate-pdf');
        Route::get('/{spo}/qr-code', [SPOController::class, 'generateQr'])->name('qr-code');
    });
