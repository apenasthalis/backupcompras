<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Orcamento;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrcamentoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->validateOrThrow($request, [
                'idempresa' => ['required', 'string', 'max:255'],
            ]);

            $user = $request->user();

            $empresa = Empresa::query()
                ->where('empcontad', $data['idempresa'])
                ->where('empstatus', 'ativo')
                ->first();

            if (!$empresa) {
                throw new ApiException('Empresa não encontrada ou inativa.', 422);
            }

            $orcamento = Orcamento::create([
                'idempresa' => $empresa->empcontad,
                'empnome' => $empresa->empnome,
                'empendereco' => $empresa->empendereco,
                'empcidade' => $empresa->empcidade,
                'empestado' => $empresa->empestado,
                'idcliente' => $this->currentClientId($user),
                'clinome' => $user->name,
                'cliendereco' => $user->endereco,
                'clicidade' => $user->cidade,
                'cliestado' => $user->estado,
                'dtcri' => now()->toDateString(),
                'status' => 'A',
                'tipstatus' => 'Orçamento Aberto',
            ]);

            return response()->json([
                'message' => 'Orçamento criado com sucesso',
                'orcamento' => $orcamento,
            ], 201);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException('Erro ao criar orçamento: ' . $e->getMessage(), 500);
        }
    }

    private function currentClientId(User $user): string
    {
        if ($user->contad) {
            return $user->contad;
        }

        $user->contad = 'C' . now()->format('YmdHis') . random_int(100, 999);
        $user->save();

        return $user->contad;
    }
}