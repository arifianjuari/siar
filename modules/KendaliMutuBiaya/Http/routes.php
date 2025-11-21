<?php

use Illuminate\Support\Facades\Route;
use Modules\KendaliMutuBiaya\Http\Controllers\KendaliMutuBiayaController;

/*
|--------------------------------------------------------------------------
| Kendali Mutu Biaya Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Kendali Mutu Biaya module.
| These routes use the 'web', 'auth', 'tenant', and 'module:kendali-mutu-biaya' middleware.
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module:kendali-mutu-biaya'])
    ->prefix('kendali-mutu-biaya')
    ->name('kendali-mutu-biaya.')
    ->group(function () {
        
        // Clinical Pathway CRUD
        Route::get('/', [KendaliMutuBiayaController::class, 'index'])->name('index');
        Route::get('/create', [KendaliMutuBiayaController::class, 'create'])->name('create');
        Route::post('/store', [KendaliMutuBiayaController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [KendaliMutuBiayaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [KendaliMutuBiayaController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [KendaliMutuBiayaController::class, 'destroy'])->name('destroy');

        // Tariff Management
        Route::get('/tariff/{id}', [KendaliMutuBiayaController::class, 'manageTariffs'])->name('tariffs');
        Route::post('/tariff/{id}/store', [KendaliMutuBiayaController::class, 'storeTariff'])->name('store-tariff');

        // Evaluation
        Route::get('/evaluate/{id}', [KendaliMutuBiayaController::class, 'evaluateCP'])->name('evaluate');
        Route::post('/evaluate/{id}/store', [KendaliMutuBiayaController::class, 'storeEvaluation'])->name('store-evaluation');
        Route::get('/evaluation/{id}', [KendaliMutuBiayaController::class, 'showEvaluation'])->name('show-evaluation');
        Route::get('/rekap', [KendaliMutuBiayaController::class, 'rekapEvaluation'])->name('rekap');

        // PDF Export
        Route::get('/pdf/{id}', [KendaliMutuBiayaController::class, 'generatePDF'])->name('pdf');
    });
