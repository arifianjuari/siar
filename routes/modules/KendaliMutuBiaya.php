<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\KendaliMutuBiaya\KendaliMutuBiayaController;

/*
|--------------------------------------------------------------------------
| Kendali Mutu Kendali Biaya Routes
|--------------------------------------------------------------------------
|
| Routes untuk modul Kendali Mutu Kendali Biaya
|
*/

Route::group([
    'prefix' => 'kendali-mutu-biaya',
    'as' => 'kendali-mutu-biaya.',
    'middleware' => ['web', 'auth', 'tenant', 'module:kendali-mutu-biaya']
], function () {
    // CRUD Clinical Pathway
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
