<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

class BaseController
{
    protected function successResponse($message, $data = [], $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'timestamp' => Carbon::now()->toIso8601String(),
            'success' => true
        ], $code);
    }

    protected function errorResponse($message, $data = [], $code = 422)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'timestamp' => Carbon::now()->toIso8601String(),
            'success' => false
        ], $code);
    }
}
