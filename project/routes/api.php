<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('google', [AuthController::class, 'googleRedirect']);
    Route::get('google/callback', [AuthController::class, 'googleCallback']);

    Route::middleware(JwtMiddleware::class)->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
