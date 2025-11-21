<?php

use Modules\ActivityManagement\Http\Controllers\ActionableItemController;
use Modules\ActivityManagement\Http\Controllers\ActivityAssigneeController;
use Modules\ActivityManagement\Http\Controllers\ActivityCommentController;
use Modules\ActivityManagement\Http\Controllers\ActivityController;
use Modules\ActivityManagement\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Activity Management Routes
|--------------------------------------------------------------------------
|
| Here is where you can register activity management routes for your application.
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module:activity-management'])
    ->prefix('activity-management')
    ->name('activity-management.')
    ->group(function () {
        // Index - View list of activities
        Route::get('activities', [ActivityController::class, 'index'])
            ->name('activities.index');

        // Create & Store - Create new activity
        Route::get('activities/create', [ActivityController::class, 'create'])
            ->middleware('check.permission:activity-management,can_create')
            ->name('activities.create');
        Route::post('activities', [ActivityController::class, 'store'])
            ->middleware('check.permission:activity-management,can_create')
            ->name('activities.store');

        // Show - View activity details
        Route::get('activities/{uuid}', [ActivityController::class, 'show'])
            ->name('activities.show');

        // Edit & Update - Edit existing activity
        Route::get('activities/{uuid}/edit', [ActivityController::class, 'edit'])
            ->middleware('check.permission:activity-management,can_edit')
            ->name('activities.edit');
        Route::patch('activities/{uuid}', [ActivityController::class, 'update'])
            ->middleware('check.permission:activity-management,can_edit')
            ->name('activities.update');

        // Delete - Remove activity
        Route::delete('activities/{uuid}', [ActivityController::class, 'destroy'])
            ->middleware('check.permission:activity-management,can_delete')
            ->name('activities.destroy');

        // Status update
        Route::put('activities/{uuid}/status', [ActivityController::class, 'updateStatus'])->name('activities.update-status');
        
        // Progress update
        Route::put('activities/{uuid}/progress', [ActivityController::class, 'updateProgress'])->name('activities.update-progress');

        // Activity Comments
        Route::get('activities/{activityUuid}/comments', [ActivityCommentController::class, 'index'])->name('comments.index');
        Route::post('activities/{activityUuid}/comments', [ActivityCommentController::class, 'store'])->name('comments.store');
        Route::post('activities/{activityUuid}/comments/{commentUuid}/reply', [ActivityCommentController::class, 'reply'])->name('comments.reply');
        Route::delete('activities/{activityUuid}/comments/{commentUuid}', [ActivityCommentController::class, 'destroy'])->name('comments.destroy');

        // Actionable Items
        Route::get('activities/{uuid}/actionable-items', [ActionableItemController::class, 'index'])
            ->name('actionable-items.index');
        Route::post('activities/{uuid}/actionable-items', [ActionableItemController::class, 'store'])
            ->name('actionable-items.store');
        Route::put('activities/{uuid}/actionable-items/{itemUuid}', [ActionableItemController::class, 'update'])
            ->name('actionable-items.update');
        Route::delete('activities/{uuid}/actionable-items/{itemUuid}', [ActionableItemController::class, 'destroy'])
            ->name('actionable-items.destroy');
        Route::put('activities/{uuid}/actionable-items/{itemUuid}/toggle', [ActionableItemController::class, 'toggle'])
            ->name('actionable-items.toggle');

        // Activity Assignees Management
        Route::get('activities/{uuid}/assignees', [ActivityAssigneeController::class, 'index'])
            ->name('assignees.index');
        Route::post('activities/{uuid}/assignees', [ActivityAssigneeController::class, 'store'])
            ->name('assignees.store');
        Route::delete('activities/{uuid}/assignees/{assigneeId}', [ActivityAssigneeController::class, 'destroy'])
            ->name('assignees.destroy');
    });
