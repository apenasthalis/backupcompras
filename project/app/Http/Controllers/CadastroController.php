<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CadastroController extends Controller
{
    public function index(): View
    {
        return view('jc-cadastro', ['usuario' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $user->name = $data['name'];
        $user->endereco = $data['endereco'] ?? null;
        $user->cidade = $data['cidade'] ?? null;
        $user->estado = $data['estado'] ?? null;
        $user->save();

        return Redirect::route('cadastro')
            ->with('success', 'Dados cadastrais atualizados com sucesso.');
    }
}