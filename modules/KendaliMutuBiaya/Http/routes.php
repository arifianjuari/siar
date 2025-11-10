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
    'middleware' => ['web', 'auth', 'tenant']
], function () {
    // Clinical Pathway Management
    Route::get('/', [KendaliMutuBiayaController::class, 'index'])->name('index');
    Route::get('/create', [KendaliMutuBiayaController::class, 'create'])->name('create');
    Route::post('/', [KendaliMutuBiayaController::class, 'store'])->name('store');
    Route::get('/{id}', [KendaliMutuBiayaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [KendaliMutuBiayaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [KendaliMutuBiayaController::class, 'update'])->name('update');
    Route::delete('/{id}', [KendaliMutuBiayaController::class, 'destroy'])->name('destroy');
});
