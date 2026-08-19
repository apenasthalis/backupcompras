<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OrcProntosController extends Controller
{
    public function index(): View
    {
        $orcamentos = Orcamento::query()
            ->where('status', 'P')
            ->where('idcliente', $this->currentClientId())
            ->orderBy('idorc')
            ->get();

        return view('jc-orc-prontos', [
            'orcamentos' => $orcamentos,
            'usuario' => auth()->user(),
        ]);
    }

    public function aprovar(int $orcamento): RedirectResponse|View
    {
        $orcamentoModel = Orcamento::query()
            ->where('idorc', $orcamento)
            ->where('idcliente', $this->currentClientId())
            ->where('status', 'P')
            ->first();

        if (!$orcamentoModel) {
            throw new ModelNotFoundException('Orçamento não encontrado.');
        }

        return view('jc-orc-aprovar', [
            'orcamento' => $orcamentoModel,
            'usuario' => auth()->user(),
        ]);
    }

    private function currentClientId(): string
    {
        $user = auth()->user();

        return (string) ($user->contad ?? $user->id);
    }
}