<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentReferenceController;
use App\Http\Controllers\Api\CorrespondenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Document Reference Routes
Route::get('/document-references', [DocumentReferenceController::class, 'getReferences']);

// Correspondence Routes
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::get('/letters', [CorrespondenceController::class, 'getLetters']);
});

// User Routes
use App\Http\Controllers\Api\UserController;

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::get('/users/search', [UserController::class, 'search'])->name('api.users.search');
});
