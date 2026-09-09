<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\OrcamentoController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware(JwtMiddleware::class)->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(JwtMiddleware::class)->group(function () {
    Route::get('empresas', [EmpresaController::class, 'index']);
    Route::get('empresas/segmentos', [EmpresaController::class, 'segmentos']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/search', [ProductController::class, 'search']);
    Route::post('user-products', [ProductController::class, 'store']);
    Route::post('orcamentos', [OrcamentoController::class, 'store']);
});
