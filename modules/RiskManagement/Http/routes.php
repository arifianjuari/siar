<?php

use Illuminate\Support\Facades\Route;
use Modules\RiskManagement\Http\Controllers\RiskReportController;
use Modules\RiskManagement\Http\Controllers\RiskAnalysisController;
use Modules\RiskManagement\Http\Controllers\RiskManagementController;

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
    ->name('modules.risk-management.')
    ->group(function () {
        // Dashboard
        Route::get('/', [RiskManagementController::class, 'index'])->name('index');
        Route::get('/dashboard', [RiskReportController::class, 'dashboard'])->name('dashboard');

        // Risk Report CRUD
        Route::get('/reports', [RiskReportController::class, 'index'])->name('risk-reports.index');
        Route::get('/reports/create', [RiskReportController::class, 'create'])->name('risk-reports.create');
        Route::post('/reports', [RiskReportController::class, 'store'])->name('risk-reports.store');
        Route::get('/reports/{id}', [RiskReportController::class, 'show'])->name('risk-reports.show');
        Route::get('/reports/{id}/edit', [RiskReportController::class, 'edit'])->name('risk-reports.edit');
        Route::put('/reports/{id}', [RiskReportController::class, 'update'])->name('risk-reports.update');
        Route::delete('/reports/{id}', [RiskReportController::class, 'destroy'])->name('risk-reports.destroy');

        // Risk Report Actions
        Route::post('/reports/{id}/approve', [RiskReportController::class, 'approve'])->name('risk-reports.approve');
        Route::post('/reports/{id}/review', [RiskReportController::class, 'review'])->name('risk-reports.review');
        Route::get('/reports/{id}/export-pdf', [RiskReportController::class, 'exportPdf'])->name('risk-reports.export-pdf');
        
        // Laporan Awal & Akhir
        Route::get('/reports/{id}/laporan-awal', [RiskReportController::class, 'laporanAwal'])->name('risk-reports.laporan-awal');
        Route::get('/reports/{id}/laporan-akhir', [RiskReportController::class, 'laporanAkhir'])->name('risk-reports.laporan-akhir');
        
        // Download Attachment
        Route::get('/reports/{id}/download-attachment/{documentId}', [RiskReportController::class, 'downloadAttachment'])->name('risk-reports.download-attachment');
        
        // Analysis Configuration
        Route::get('/analysis-config', [RiskManagementController::class, 'showAnalysisConfig'])->name('analysis-config');
        Route::post('/analysis-config', [RiskManagementController::class, 'saveAnalysisConfig'])->name('save-analysis-config');
        
        // Risk Analysis
        Route::get('/reports/{reportId}/analysis', [RiskAnalysisController::class, 'index'])->name('risk-analysis.index');
        Route::get('/reports/{reportId}/analysis/create', [RiskAnalysisController::class, 'create'])->name('risk-analysis.create');
        Route::post('/reports/{reportId}/analysis', [RiskAnalysisController::class, 'store'])->name('risk-analysis.store');
        Route::get('/reports/{reportId}/analysis/{id}', [RiskAnalysisController::class, 'show'])->name('risk-analysis.show');
        Route::get('/reports/{reportId}/analysis/{id}/edit', [RiskAnalysisController::class, 'edit'])->name('risk-analysis.edit');
        Route::put('/reports/{reportId}/analysis/{id}', [RiskAnalysisController::class, 'update'])->name('risk-analysis.update');
        Route::delete('/reports/{reportId}/analysis/{id}', [RiskAnalysisController::class, 'destroy'])->name('risk-analysis.destroy');
        Route::get('/reports/{reportId}/analysis/{id}/qr-code', [RiskAnalysisController::class, 'generateQr'])->name('risk-analysis.qr-code');
    });
