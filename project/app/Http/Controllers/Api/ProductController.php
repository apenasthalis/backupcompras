<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\UserProduct;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            return response()->json(
                Product::orderBy('name')->get(['id', 'name'])
            );
        } catch (Exception $e) {
            throw new ApiException('Erro ao carregar produtos: ' . $e->getMessage(), 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $term = trim((string) $request->query('q', ''));
            $segmentoId = $request->query('segmento_id');

            if ($term === '') {
                return response()->json([]);
            }

            $query = Product::where('name', 'ilike', '%' . $term . '%');

            if ($segmentoId) {
                $query->where('segmento_id', $segmentoId);
            }

            $products = $query
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'name']);

            return response()->json($products);
        } catch (Exception $e) {
            throw new ApiException('Erro ao pesquisar produtos: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->validateOrThrow($request, [
                'product_id' => 'required|exists:products,id',
                'orcamento_id' => 'nullable|exists:orcamentos,idorc',
                'description' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'unit' => 'required|string|max:50',
                'quantity' => 'required|numeric|min:0.01',
            ]);

            $userProduct = UserProduct::create([
                'product_id' => $data['product_id'],
                'user_id' => $request->user()->id,
                'orcamento_id' => $data['orcamento_id'] ?? null,
                'description' => $data['description'],
                'brand' => $data['brand'],
                'unit' => $data['unit'],
                'quantity' => $data['quantity'],
            ]);

            return response()->json(
                $userProduct->load('product'),
                201
            );
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException('Erro ao salvar item: ' . $e->getMessage(), 500);
        }
    }
}
