<?php

use Modules\UserManagement\Http\Controllers\UserController;
use Modules\UserManagement\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Management Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the User Management module.
| These routes use the 'web', 'auth', 'tenant', and 'module.permission' middleware.
|
*/

// Base group dengan middleware dasar
Route::middleware(['web', 'auth', 'tenant', 'module:user-management'])
    ->prefix('user-management')
    ->name('modules.user-management.')
    ->group(function () {

        // User routes dengan permission checks
        // Index - Semua user dapat melihat list (dengan can_view)
        Route::get('users', [UserController::class, 'index'])->name('users.index');

        // Create & Store - Memerlukan izin can_create
        Route::get('users/create', [UserController::class, 'create'])
            ->middleware('check.permission:user-management,can_create')
            ->name('users.create');
        Route::post('users', [UserController::class, 'store'])
            ->middleware('check.permission:user-management,can_create')
            ->name('users.store');

        // Show - Memerlukan izin can_view
        Route::get('users/{id}', [UserController::class, 'show'])
            ->name('users.show');

        // Edit & Update - Memerlukan izin can_edit
        Route::get('users/{id}/edit', [UserController::class, 'edit'])
            ->middleware('check.permission:user-management,can_edit')
            ->name('users.edit');
        Route::put('users/{id}', [UserController::class, 'update'])
            ->middleware('check.permission:user-management,can_edit')
            ->name('users.update');

        // Delete - Memerlukan izin can_delete
        Route::delete('users/{id}', [UserController::class, 'destroy'])
            ->middleware('check.permission:user-management,can_delete')
            ->name('users.destroy');

        // Role routes dengan permission checks
        // Index - Semua user dapat melihat list (dengan can_view)
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');

        // Create & Store - Memerlukan izin can_create
        Route::get('roles/create', [RoleController::class, 'create'])
            ->middleware('check.permission:user-management,can_create')
            ->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])
            ->middleware('check.permission:user-management,can_create')
            ->name('roles.store');

        // Show - Memerlukan izin can_view
        Route::get('roles/{id}', [RoleController::class, 'show'])
            ->name('roles.show');

        // Edit & Update - Memerlukan izin can_edit
        Route::get('roles/{id}/edit', [RoleController::class, 'edit'])
            ->middleware('check.permission:user-management,can_edit')
            ->name('roles.edit');
        Route::put('roles/{id}', [RoleController::class, 'update'])
            ->middleware('check.permission:user-management,can_edit')
            ->name('roles.update');

        // Delete - Memerlukan izin can_delete
        Route::delete('roles/{id}', [RoleController::class, 'destroy'])
            ->middleware('check.permission:user-management,can_delete')
            ->name('roles.destroy');
    });
