<?php

use Modules\DocumentManagement\Http\Controllers\DocumentController;
use Modules\DocumentManagement\Http\Controllers\DocumentManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Document Management Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Document Management module.
| These routes use the 'web', 'auth', 'tenant', and 'module:document-management' middleware.
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module:document-management'])
    ->prefix('document-management')
    ->name('modules.document-management.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DocumentManagementController::class, 'dashboard'])->name('dashboard');

        // Documents routes
        Route::resource('documents', DocumentController::class)
            ->except(['show', 'edit', 'update']);
        
        // Custom routes with additional middleware
        Route::get('documents/{document}', [DocumentController::class, 'show'])
            ->name('documents.show')
            ->middleware(['check.permission:document-management,can_view', 'tenant.document']);

        Route::get('documents/{document}/edit', [DocumentController::class, 'edit'])
            ->name('documents.edit')
            ->middleware(['check.permission:document-management,can_edit', 'tenant.document']);

        Route::put('documents/{document}', [DocumentController::class, 'update'])
            ->name('documents.update')
            ->middleware(['check.permission:document-management,can_edit', 'tenant.document']);

        // Document Revision Route
        Route::post('documents/{id}/revise', [DocumentController::class, 'revise'])
            ->name('documents.revise')
            ->middleware(['check.permission:document-management,can_create', 'tenant.document']);

        // Documents By Tag Route
        Route::get('documents-by-tag/{slug}', [DocumentManagementController::class, 'documentsByTag'])
            ->name('documents-by-tag');

        // Documents By Type Route
        Route::get('documents-by-type/{type}', [DocumentManagementController::class, 'documentsByType'])
            ->name('documents-by-type');
    });
