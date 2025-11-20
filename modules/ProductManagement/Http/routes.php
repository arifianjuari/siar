<?php

use Modules\ProductManagement\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product Management Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Product Management module.
| These routes use the 'web', 'auth', 'tenant', and 'module:product-management' middleware.
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module.permission:PRODUCT_MANAGEMENT'])
    ->prefix('product-management')
    ->name('modules.product-management.')
    ->group(function () {
        
        // Product resource routes
        Route::resource('products', ProductController::class);
    });
