<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // product routes
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::prefix('products')->group(function () {
        Route::post('data', [App\Http\Controllers\ProductController::class, 'data'])->name('products.data');
    });

    // stock product routes
    Route::resource('stock-products', App\Http\Controllers\StockProductController::class);
    Route::prefix('stock-products')->group(function () {
        Route::post('data', [App\Http\Controllers\StockProductController::class, 'data'])->name('stock-products.data');
    });

    // transaction history routes
    Route::resource('transaction-history', App\Http\Controllers\TransactionHistoryController::class);
    Route::prefix('transaction-history')->group(function () {
        Route::post('data', [App\Http\Controllers\TransactionHistoryController::class, 'data'])->name('transaction-history.data');
        Route::post('generateBatch', [App\Http\Controllers\TransactionHistoryController::class, 'generateBatch'])->name('transaction-history.generateBatch');
    });
});