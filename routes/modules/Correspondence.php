<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Correspondence\CorrespondenceController;
use App\Http\Controllers\Modules\Correspondence\ReportController;

/*
|--------------------------------------------------------------------------
| Correspondence Module Routes
|--------------------------------------------------------------------------
|
| Di sini adalah tempat untuk mendefinisikan route modul Korespondensi
| Semua rute akan otomatis diberi prefiks "modules/correspondence"
|
*/

// Rute yang dapat diakses setelah login dan memiliki akses ke modul korespondensi
Route::middleware(['auth', 'tenant', 'module:correspondence'])->prefix('correspondence')->name('correspondence.')->group(function () {
    // Dashboard Korespondensi
    Route::get('/dashboard', [CorrespondenceController::class, 'dashboard'])->name('dashboard');

    // Manajemen Surat/Nota Dinas
    Route::resource('letters', CorrespondenceController::class);

    // Export Surat/Nota Dinas
    Route::get('/letters/{id}/export-pdf', [CorrespondenceController::class, 'exportPdf'])->name('letters.export-pdf');
    Route::get('/letters/{id}/export-word', [CorrespondenceController::class, 'exportWord'])->name('letters.export-word');

    // Route untuk melihat QR Code
    Route::get('/letters/{id}/qr-code', [CorrespondenceController::class, 'generateQr'])->name('letters.qr-code');

    // Filter dan pencarian
    Route::get('/search', [CorrespondenceController::class, 'search'])->name('search');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});
