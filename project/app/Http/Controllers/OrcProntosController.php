<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use App\Models\UserProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OrcProntosController extends Controller
{
    public function index(): View
    {
        $orcamentos = Orcamento::query()
            ->where('status', 'P')
            ->where('idcliente', $this->currentClientId())
            ->with('itens')
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
            ->with('itens')
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

    public function salvar(int $orcamento, Request $request): RedirectResponse
    {
        $orcamentoModel = Orcamento::query()
            ->where('idorc', $orcamento)
            ->where('idcliente', $this->currentClientId())
            ->where('status', 'P')
            ->firstOrFail();

        $data = $request->validate([
            'desconto' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'itens' => ['nullable', 'array'],
            'itens.*.id' => ['nullable', 'integer'],
            'itens.*.description' => ['required', 'string', 'max:255'],
            'itens.*.brand' => ['nullable', 'string', 'max:255'],
            'itens.*.unit' => ['nullable', 'string', 'max:50'],
            'itens.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'itens.*.preco_lojista' => ['required', 'numeric', 'min:0'],
            'itens.*.preco_cliente' => ['required', 'numeric', 'min:0'],
        ]);

        $orcamentoModel->update(['desconto' => $data['desconto'] ?? 0]);

        DB::transaction(function () use ($orcamentoModel, $data): void {
            foreach ($data['itens'] ?? [] as $item) {
                UserProduct::query()
                    ->where('id', $item['id'] ?? 0)
                    ->where('orcamento_id', $orcamentoModel->idorc)
                    ->update([
                        'description' => $item['description'],
                        'brand' => $item['brand'] ?? '',
                        'unit' => $item['unit'] ?? '',
                        'quantity' => $item['quantity'],
                        'preco_lojista' => $item['preco_lojista'],
                        'preco_cliente' => $item['preco_cliente'],
                    ]);
            }
        });

        return Redirect::route('orc-prontos.aprovar', ['orcamento' => $orcamentoModel->idorc])
            ->with('success', 'Orçamento atualizado com sucesso.');
    }

    private function currentClientId(): string
    {
        $user = auth()->user();

        return (string) ($user->contad ?? $user->id);
    }
}