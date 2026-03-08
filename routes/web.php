<?php

use Illuminate\Support\Facades\Route;

/**
 * Web Routes - Serve Vue Frontend
 * 
 * All web requests are handled by the Vue SPA
 * API routes are in routes/api.php
 */

// Serve Vue SPA for all non-API routes
Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');
