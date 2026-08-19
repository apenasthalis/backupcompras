<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OrcAbertoController extends Controller
{
    public function index(): View
    {
        $orcamentos = Orcamento::query()
            ->where('status', 'A')
            ->where('idcliente', $this->currentClientId())
            ->orderBy('idorc')
            ->get();

        return view('jc-orc-abertos', [
            'orcamentos' => $orcamentos,
            'usuario' => auth()->user(),
        ]);
    }

    public function cobrar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'orcamentos' => ['required', 'array', 'min:1'],
            'orcamentos.*' => ['required', 'integer'],
        ]);

        $ids = array_map('intval', $data['orcamentos']);

        $cobrados = DB::transaction(function () use ($ids): int {
            return Orcamento::query()
                ->whereIn('idorc', $ids)
                ->where('idcliente', $this->currentClientId())
                ->where('status', 'A')
                ->update([
                    'status' => 'C',
                    'tipstatus' => 'Orçamento Cobrado',
                ]);
        });

        if ($cobrados === 0) {
            return Redirect::route('orc-abertos')
                ->with('error', 'Nenhum orçamento pôde ser cobrado.');
        }

        return Redirect::route('orc-abertos')
            ->with('success', $cobrados.' orçamento(s) cobrado(s) com sucesso.');
    }

    public function editar(int $orcamento): RedirectResponse
    {
        $orcamentoModel = Orcamento::query()
            ->where('idorc', $orcamento)
            ->where('idcliente', $this->currentClientId())
            ->where('status', 'A')
            ->firstOrFail();

        $orcamentoModel->update([
            'status' => 'A',
            'tipstatus' => 'Orçamento Aberto',
        ]);

        return Redirect::route('editar-orc', ['orcamento' => $orcamentoModel->idorc]);
    }

    public function editarPagina(int $orcamento): View
    {
        return view('jc-editar-orc', ['orcamento' => $orcamento]);
    }

    private function currentClientId(): string
    {
        $user = auth()->user();

        return (string) ($user->contad ?? $user->id);
    }
}
