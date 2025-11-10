<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\RiskManagement\RiskReportController;

/*
|--------------------------------------------------------------------------
| Risk Management Module Routes
|--------------------------------------------------------------------------
|
| Routes untuk Manajemen Risiko
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module:risk-management'])
    ->prefix('risk-management')
    ->name('risk-management.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [RiskReportController::class, 'dashboard'])->name('dashboard');

        // Risk Report
        Route::get('/reports', [RiskReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [RiskReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [RiskReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{id}', [RiskReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{id}/edit', [RiskReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{id}', [RiskReportController::class, 'update'])->name('reports.update');
        Route::delete('/reports/{id}', [RiskReportController::class, 'destroy'])->name('reports.destroy');

        // Laporan Awal & Akhir
        Route::get('/reports/{id}/laporan-awal', [RiskReportController::class, 'laporanAwal'])->name('reports.laporan-awal');
        Route::get('/reports/{id}/laporan-akhir', [RiskReportController::class, 'laporanAkhir'])->name('reports.laporan-akhir');
    });
