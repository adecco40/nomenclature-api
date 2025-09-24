<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionRenderer
{
    /**
     * Преобразование исключения в JSON-ответ
     */
    public static function render(Throwable $e): JsonResponse
    {
        // Ошибки валидации
        if ($e instanceof ValidationException) {
            return response()->json([
                'success'   => false,
                'message'   => 'Validation error',
                'errors'    => $e->errors(),
                'timestamp' => now()->toIso8601String(),
            ], 422);
        }

        // HTTP-ошибки (404, 403 и т.д.)
        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage() ?: 'HTTP Error',
                'type'      => class_basename($e),
                'code'      => $e->getStatusCode(),
                'timestamp' => now()->toIso8601String(),
            ], $e->getStatusCode());
        }

        // Все остальные (500 и неожиданные)
        return response()->json([
            'success'   => false,
            'message'   => $e->getMessage() ?: 'Server Error',
            'type'      => class_basename($e),
            'code'      => 500,
            'timestamp' => now()->toIso8601String(),
        ], 500);
    }
}
