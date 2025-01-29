<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;


trait ResponseTrait
{
    public function sendSuccess(string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
    public function sendError(string $message, array $errors = [], int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
