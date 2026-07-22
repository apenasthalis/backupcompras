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

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->validateOrThrow($request, [
                'product_id' => 'required|exists:products,id',
                'description' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'unit' => 'required|string|max:50',
                'quantity' => 'required|numeric|min:0.01',
            ]);

            $userProduct = UserProduct::create([
                'product_id' => $data['product_id'],
                'user_id' => $request->user()->id,
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
