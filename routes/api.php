<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionHistoryController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('products', ProductController::class);
    Route::get('/locations', [LocationController::class, 'index']);

    Route::get('/transaction-history', [TransactionHistoryController::class, 'index']);
    Route::post('/transaction-history/generate-batch', [TransactionHistoryController::class, 'generateBatch']);
});
