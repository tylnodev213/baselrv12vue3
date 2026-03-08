<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('refresh-token');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    Route::resource('products', ProductController::class);
});
