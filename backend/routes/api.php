<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnalyzeController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\Admin\UserAdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/analyze', AnalyzeController::class);

    Route::apiResource('reviews', ReviewController::class);

    Route::get('/dashboard', DashboardController::class);

    Route::get('/export/reviews.csv', [ExportController::class, 'reviewsCsv']);
    
    Route::get('/reviews', [ReviewController::class, 'index']);

    Route::post('/reviews', [ReviewController::class, 'store']);
    
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    Route::middleware('admin')->group(function () {
    Route::get('/admin/users', [UserAdminController::class, 'index']);
    });
});
