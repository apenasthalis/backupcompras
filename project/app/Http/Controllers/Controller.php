<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class Controller
{
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        $response = compact('message');
        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $status);
    }

    protected function error(string $message, int $status = 400, ?array $errors = null): JsonResponse
    {
        $response = compact('message');
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status);
    }

    protected function validateOrThrow(Request $request, array $rules): array
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new ApiException(
                message: 'Erro de validação',
                statusCode: 422,
                errors: $validator->errors()->toArray()
            );
        }

        return $validator->validated();
    }

    protected function runOrThrow(callable $callback, string $errorMessage = 'Erro interno do servidor')
    {
        try {
            return $callback();
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException(
                message: $errorMessage . ': ' . $e->getMessage(),
                statusCode: 500,
                previous: $e
            );
        }
    }
}
