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

    // Profile & My Team
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show']);
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update']);
    Route::get('/my-team', [\App\Http\Controllers\ProfileController::class, 'teamMembers']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard-stats', [\App\Http\Controllers\Admin\DashboardController::class, 'index']);
        Route::apiResource('teams', \App\Http\Controllers\Admin\TeamController::class);
        Route::apiResource('users', \App\Http\Controllers\Admin\UserController::class);
    });
});
