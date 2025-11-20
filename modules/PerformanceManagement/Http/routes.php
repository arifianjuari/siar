<?php

use Modules\PerformanceManagement\Http\Controllers\DashboardController;
use Modules\PerformanceManagement\Http\Controllers\PerformanceIndicatorController;
use Modules\PerformanceManagement\Http\Controllers\PerformanceScoreController;
use Modules\PerformanceManagement\Http\Controllers\PerformanceTemplateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Performance Management Routes
|--------------------------------------------------------------------------
|
| Routes untuk modul Performance Management (KPI Individu)
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module:performance-management'])
    ->prefix('performance-management')
    ->name('performance-management.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('index');
        
        // Performance Indicators (Indikator Kinerja)
        Route::get('indicators', [PerformanceIndicatorController::class, 'index'])
            ->name('indicators.index');
        Route::get('indicators/create', [PerformanceIndicatorController::class, 'create'])
            ->middleware('check.permission:performance-management,can_create')
            ->name('indicators.create');
        Route::post('indicators', [PerformanceIndicatorController::class, 'store'])
            ->middleware('check.permission:performance-management,can_create')
            ->name('indicators.store');
        Route::get('indicators/{id}', [PerformanceIndicatorController::class, 'show'])
            ->name('indicators.show');
        Route::get('indicators/{id}/edit', [PerformanceIndicatorController::class, 'edit'])
            ->middleware('check.permission:performance-management,can_edit')
            ->name('indicators.edit');
        Route::put('indicators/{id}', [PerformanceIndicatorController::class, 'update'])
            ->middleware('check.permission:performance-management,can_edit')
            ->name('indicators.update');
        Route::delete('indicators/{id}', [PerformanceIndicatorController::class, 'destroy'])
            ->middleware('check.permission:performance-management,can_delete')
            ->name('indicators.destroy');

        // Performance Scores (Nilai Kinerja)
        Route::get('scores', [PerformanceScoreController::class, 'index'])
            ->name('scores.index');
        Route::get('scores/create', [PerformanceScoreController::class, 'create'])
            ->middleware('check.permission:performance-management,can_create')
            ->name('scores.create');
        Route::post('scores', [PerformanceScoreController::class, 'store'])
            ->middleware('check.permission:performance-management,can_create')
            ->name('scores.store');
        Route::get('scores/{id}', [PerformanceScoreController::class, 'show'])
            ->name('scores.show');
        Route::get('scores/{id}/edit', [PerformanceScoreController::class, 'edit'])
            ->middleware('check.permission:performance-management,can_edit')
            ->name('scores.edit');
        Route::put('scores/{id}', [PerformanceScoreController::class, 'update'])
            ->middleware('check.permission:performance-management,can_edit')
            ->name('scores.update');
        Route::delete('scores/{id}', [PerformanceScoreController::class, 'destroy'])
            ->middleware('check.permission:performance-management,can_delete')
            ->name('scores.destroy');

        // Performance Templates (Template KPI)
        Route::get('templates', [PerformanceTemplateController::class, 'index'])
            ->name('templates.index');
        Route::get('templates/create', [PerformanceTemplateController::class, 'create'])
            ->middleware('check.permission:performance-management,can_create')
            ->name('templates.create');
        Route::post('templates', [PerformanceTemplateController::class, 'store'])
            ->middleware('check.permission:performance-management,can_create')
            ->name('templates.store');
        Route::get('templates/{id}', [PerformanceTemplateController::class, 'show'])
            ->name('templates.show');
        Route::get('templates/{id}/edit', [PerformanceTemplateController::class, 'edit'])
            ->middleware('check.permission:performance-management,can_edit')
            ->name('templates.edit');
        Route::put('templates/{id}', [PerformanceTemplateController::class, 'update'])
            ->middleware('check.permission:performance-management,can_edit')
            ->name('templates.update');
        Route::delete('templates/{id}', [PerformanceTemplateController::class, 'destroy'])
            ->middleware('check.permission:performance-management,can_delete')
            ->name('templates.destroy');
    });
