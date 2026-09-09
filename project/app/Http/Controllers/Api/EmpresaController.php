<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Segmento;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $query = Empresa::query()
                ->where('empstatus', 'ativo');

            if ($request->filled('segmento_id')) {
                $query->where('segmento_id', $request->query('segmento_id'));
            }

            $empresas = $query
                ->leftJoin('segmentos', 'segmentos.id', '=', 'empresas.segmento_id')
                ->orderBy('empnome')
                ->get([
                    'empresas.empcontad',
                    'empresas.empnome',
                    'empresas.empendereco',
                    'empresas.empcidade',
                    'empresas.empestado',
                    'empresas.segmento_id',
                    'segmentos.name as segmento',
                ]);

            if ($user && ($user->cidade || $user->estado)) {
                $cidade = self::normalizar($user->cidade);
                $estado = self::normalizar($user->estado);

                $empresas = $empresas->filter(function (Empresa $empresa) use ($cidade, $estado) {
                    if ($cidade !== '' && self::normalizar($empresa->empcidade) !== $cidade) {
                        return false;
                    }
                    if ($estado !== '' && self::normalizar($empresa->empestado) !== $estado) {
                        return false;
                    }

                    return true;
                })->values();
            }

            return response()->json($empresas);
        } catch (Exception $e) {
            throw new ApiException('Erro ao listar empresas: '.$e->getMessage(), 500);
        }
    }

    private static function normalizar(?string $valor): string
    {
        if ($valor === null) {
            return '';
        }

        $valor = mb_strtolower(trim($valor));
        $valor = strtr($valor, [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
        ]);

        return $valor;
    }

    public function segmentos(Request $request): JsonResponse
    {
        try {
            $segmentos = Segmento::orderBy('name')->get(['id', 'name']);

            return response()->json($segmentos);
        } catch (Exception $e) {
            throw new ApiException('Erro ao listar segmentos: '.$e->getMessage(), 500);
        }
    }
}
