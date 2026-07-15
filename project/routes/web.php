<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/menu', function () {
    return view('jc-menu');
})->name('menu');

Route::get('/menu-completo', function () {
    return view('jc-menu-completo');
})->name('menu-completo');
