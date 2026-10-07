<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OrcProntosController extends Controller
{
    private const STATUS_MODIFICADO = 'M';

    private const TIPSTATUS_MODIFICADO = 'Orçamento Modificado';

    private const STATUS_DESCONTO = 'D';

    private const TIPSTATUS_DESCONTO = 'Desconto Solicitado';

    private const STATUS_COBRADO = 'C';

    private const TIPSTATUS_COBRADO = 'Orçamento Cobrado';

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

    public function avancar(int $orcamento, Request $request): RedirectResponse
    {
        $orcamentoModel = $this->buscarPronto($orcamento);

        $data = $request->validate([
            'frete' => ['nullable', 'numeric', 'min:0'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.id' => ['nullable', 'integer'],
            'itens.*.product_id' => ['nullable', 'integer'],
            'itens.*.description' => ['required', 'string', 'max:255'],
            'itens.*.brand' => ['nullable', 'string', 'max:255'],
            'itens.*.unit' => ['nullable', 'string', 'max:50'],
            'itens.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'itens.*.preco_cliente' => ['nullable', 'numeric', 'min:0'],
            'deletar' => ['nullable', 'array'],
            'deletar.*' => ['integer'],
            'solicitar_desconto' => ['nullable', 'boolean'],
        ]);

        $frete = isset($data['frete']) && $data['frete'] !== ''
            ? (float) $data['frete']
            : (float) $orcamentoModel->frete;
        $descontoSolicitado = (bool) ($data['solicitar_desconto'] ?? false);

        $quantidadesAtuais = $orcamentoModel->itens()->pluck('quantity', 'id');
        $usuario = auth()->user();

        $itemIncluido = false;
        $quantidadeAumentada = false;

        DB::transaction(function () use ($orcamentoModel, $data, $quantidadesAtuais, $usuario, &$itemIncluido, &$quantidadeAumentada): void {
            if (!empty($data['deletar'])) {
                UserProduct::query()
                    ->where('orcamento_id', $orcamentoModel->idorc)
                    ->whereIn('id', $data['deletar'])
                    ->delete();
            }

            foreach ($data['itens'] as $item) {
                $quantidade = (float) $item['quantity'];

                if (empty($item['id'])) {
                    $productId = isset($item['product_id']) && $item['product_id'] !== ''
                        ? (int) $item['product_id']
                        : null;

                    UserProduct::create([
                        'product_id' => $this->productIdParaItem($productId, $item['description']),
                        'user_id' => $usuario->id,
                        'orcamento_id' => $orcamentoModel->idorc,
                        'description' => $item['description'],
                        'brand' => $item['brand'] ?? '',
                        'unit' => $item['unit'] ?? '',
                        'quantity' => $quantidade,
                        'preco_cliente' => $item['preco_cliente'] ?? 0,
                    ]);

                    $itemIncluido = true;

                    continue;
                }

                if (!$quantidadesAtuais->has($item['id'])) {
                    continue;
                }

                if ($quantidade > (float) $quantidadesAtuais[$item['id']]) {
                    $quantidadeAumentada = true;
                }

                UserProduct::query()
                    ->where('id', $item['id'])
                    ->where('orcamento_id', $orcamentoModel->idorc)
                    ->update([
                        'quantity' => $quantidade,
                    ]);
            }
        });

        $orcamentoModel->update([
            'frete' => $frete,
            'solicita_desconto' => $descontoSolicitado,
        ]);

        if ($itemIncluido || $quantidadeAumentada) {
            $orcamentoModel->update([
                'status' => self::STATUS_MODIFICADO,
                'tipstatus' => self::TIPSTATUS_MODIFICADO,
            ]);

            $aviso = $descontoSolicitado
                ? ' O lojista também verá a sua solicitação de desconto.'
                : '';

            return Redirect::route('orc-prontos')
                ->with('success', 'Orçamento #'.$orcamentoModel->idorc.' enviado como modificado. Aguarde a análise da empresa.'.$aviso);
        }

        if ($descontoSolicitado) {
            $orcamentoModel->update([
                'status' => self::STATUS_DESCONTO,
                'tipstatus' => self::TIPSTATUS_DESCONTO,
            ]);

            return Redirect::route('orc-prontos')
                ->with('success', 'Orçamento #'.$orcamentoModel->idorc.' enviado com solicitação de desconto. Aguarde a análise da empresa.');
        }

        $orcamentoModel->update([
            'status' => self::STATUS_COBRADO,
            'tipstatus' => self::TIPSTATUS_COBRADO,
        ]);

        return Redirect::route('orc-abertos')
            ->with('success', 'Orçamento #'.$orcamentoModel->idorc.' aprovado e enviado para cobrança.');
    }

    private function productIdParaItem(?int $productId, string $descricao): int
    {
        if ($productId) {
            $existente = Product::query()->whereKey($productId)->value('id');

            if ($existente) {
                return (int) $existente;
            }
        }

        $existente = Product::query()->where('name', $descricao)->value('id');

        if ($existente) {
            return (int) $existente;
        }

        return (int) Product::create(['name' => $descricao])->id;
    }

    private function buscarPronto(int $orcamento): Orcamento
    {
        return Orcamento::query()
            ->where('idorc', $orcamento)
            ->where('idcliente', $this->currentClientId())
            ->where('status', 'P')
            ->firstOrFail();
    }

    private function currentClientId(): string
    {
        $user = auth()->user();

        return (string) ($user->contad ?? $user->id);
    }
}