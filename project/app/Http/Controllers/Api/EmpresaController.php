<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Exception;
use Illuminate\Http\JsonResponse;

class EmpresaController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $empresas = Empresa::query()
                ->where('empstatus', 'ativo')
                ->orderBy('empnome')
                ->get(['empcontad', 'empnome', 'empendereco', 'empcidade', 'empestado']);

            return response()->json($empresas);
        } catch (Exception $e) {
            throw new ApiException('Erro ao listar empresas: ' . $e->getMessage(), 500);
        }
    }
}