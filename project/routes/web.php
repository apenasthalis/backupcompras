<?php

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::middleware(JwtMiddleware::class)->group(function () {
    Route::get('/menu', function () {
        return view('jc-menu');
    })->name('menu');

    Route::get('/menu-completo', function () {
        return view('jc-menu-completo');
    })->name('menu-completo');

    Route::get('/imagens', function () {
        return view('jc-imagens');
    })->name('imagens');

    Route::get('/jc-criarorc', function () {
        return view('jc-criarorc');
    })->name('criarorc');
});
