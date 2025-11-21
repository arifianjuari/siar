<?php

use Illuminate\Support\Facades\Route;
use Modules\Correspondence\Http\Controllers\CorrespondenceController;
use Modules\Correspondence\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Correspondence Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Correspondence module.
| These routes use the 'web', 'auth', 'tenant', and 'module:correspondence-management' middleware.
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module.permission:correspondence-management'])
    ->prefix('correspondence')
    ->name('modules.correspondence.')
    ->group(function () {
        
        // Dashboard - Root & /dashboard
        Route::get('/', [CorrespondenceController::class, 'dashboard'])->name('index');
        Route::get('/dashboard', [CorrespondenceController::class, 'dashboard'])->name('dashboard');

        // Letters Resource Routes
        Route::resource('letters', CorrespondenceController::class);

        // Export Routes
        Route::get('/letters/{id}/export-pdf', [CorrespondenceController::class, 'exportPdf'])->name('letters.export-pdf');
        Route::get('/letters/{id}/export-word', [CorrespondenceController::class, 'exportWord'])->name('letters.export-word');

        // QR Code Routes
        Route::get('/letters/{id}/qr-code', [CorrespondenceController::class, 'generateQr'])->name('letters.qr-code');
        Route::get('/letters/{id}/qr-code-base64', [CorrespondenceController::class, 'generateQrBase64'])->name('letters.qr-code-base64');

        // QR Code Test
        Route::get('/qr-test', function () {
            return view('correspondence::letters.qr-test');
        })->name('qr-test');

        // Search and Filter
        Route::get('/search', [CorrespondenceController::class, 'search'])->name('search');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });
