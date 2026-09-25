<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use App\Models\UserProduct;
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
            ->whereIn('status', ['A', 'C'])
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
        $orcamentoModel = Orcamento::query()
            ->with('itens')
            ->where('idorc', $orcamento)
            ->where('idcliente', $this->currentClientId())
            ->firstOrFail();

        return view('jc-editar-orc', ['orcamento' => $orcamentoModel]);
    }

    public function cobrarUnico(int $orcamento): RedirectResponse
    {
        $orcamentoModel = Orcamento::query()
            ->where('idorc', $orcamento)
            ->where('idcliente', $this->currentClientId())
            ->where('status', 'A')
            ->firstOrFail();

        $orcamentoModel->update([
            'status' => 'C',
            'tipstatus' => 'Orçamento Cobrado',
        ]);

        return Redirect::route('orc-abertos')
            ->with('success', 'Orçamento #' . $orcamentoModel->idorc . ' cobrado com sucesso.');
    }

    public function salvar(int $orcamento, Request $request): RedirectResponse
    {
        $orcamentoModel = Orcamento::query()
            ->where('idorc', $orcamento)
            ->where('idcliente', $this->currentClientId())
            ->where('status', 'A')
            ->firstOrFail();

        $data = $request->validate([
            'itens' => ['nullable', 'array'],
            'itens.*.id' => ['nullable', 'integer'],
            'itens.*.description' => ['required', 'string', 'max:255'],
            'itens.*.brand' => ['nullable', 'string', 'max:255'],
            'itens.*.unit' => ['nullable', 'string', 'max:50'],
            'itens.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'deletar' => ['nullable', 'array'],
            'deletar.*' => ['integer'],
        ]);

        $itens = $data['itens'] ?? [];
        $deletar = $data['deletar'] ?? [];
        $user = auth()->user();

        DB::transaction(function () use ($orcamentoModel, $itens, $deletar, $user): void {
            if (!empty($deletar)) {
                UserProduct::query()
                    ->where('orcamento_id', $orcamentoModel->idorc)
                    ->whereIn('id', $deletar)
                    ->delete();
            }

            foreach ($itens as $item) {
                $payload = [
                    'description' => $item['description'],
                    'brand' => $item['brand'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'quantity' => $item['quantity'],
                ];

                if (!empty($item['id'])) {
                    UserProduct::query()
                        ->where('id', $item['id'])
                        ->where('orcamento_id', $orcamentoModel->idorc)
                        ->update($payload);
                } else {
                    UserProduct::create([
                        'product_id' => 1,
                        'user_id' => $user->id,
                        'orcamento_id' => $orcamentoModel->idorc,
                        'description' => $item['description'],
                        'brand' => $item['brand'] ?? '',
                        'unit' => $item['unit'] ?? '',
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
        });

        return Redirect::route('editar-orc', ['orcamento' => $orcamentoModel->idorc])
            ->with('success', 'Orçamento atualizado com sucesso.');
    }

    private function currentClientId(): string
    {
        $user = auth()->user();

        return (string) ($user->contad ?? $user->id);
    }
}
