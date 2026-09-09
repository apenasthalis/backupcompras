<?php

use App\Http\Controllers\CadastroController;
use App\Http\Controllers\OrcAbertoController;
use App\Http\Controllers\OrcProntosController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');

Route::get('/login', function () {
    return view('login');
});

Route::middleware(JwtMiddleware::class)->group(function () {
    Route::get('/menu', function () {
        return view('jc-menu');
    })->name('menu');

    Route::get('/jc-euprecisode', function () {
        return view('jc-euprecisode');
    })->name('euprecisode');

    Route::get('/cadastro', [CadastroController::class, 'index'])->name('cadastro');
    Route::post('/cadastro', [CadastroController::class, 'update'])->name('cadastro.update');

    Route::get('/menu-completo', function () {
        return view('jc-menu-completo');
    })->name('menu-completo');

    Route::get('/imagens', function () {
        return view('jc-imagens');
    })->name('imagens');

    Route::get('/jc-criarorc', function () {
        return view('jc-criarorc');
    })->name('criarorc');

    Route::get('/orc-abertos', [OrcAbertoController::class, 'index'])->name('orc-abertos');
    Route::post('/orc-abertos/cobrar', [OrcAbertoController::class, 'cobrar'])->name('orc-abertos.cobrar');
    Route::post('/orc-abertos/{orcamento}/editar', [OrcAbertoController::class, 'editar'])->name('orc-abertos.editar');
    Route::get('/jc-editarorc/{orcamento}', [OrcAbertoController::class, 'editarPagina'])->name('editar-orc');
    Route::post('/jc-editarorc/{orcamento}/cobrar', [OrcAbertoController::class, 'cobrarUnico'])->name('editar-orc.cobrar');
    Route::post('/jc-editarorc/{orcamento}/salvar', [OrcAbertoController::class, 'salvar'])->name('editar-orc.salvar');

    Route::get('/orc-prontos', [OrcProntosController::class, 'index'])->name('orc-prontos');
    Route::get('/orc-prontos/{orcamento}/aprovar', [OrcProntosController::class, 'aprovar'])->name('orc-prontos.aprovar');
});
