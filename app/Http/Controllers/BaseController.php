<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Exception;

class BaseController extends Controller
{
    /**
     * Handle requests with a try-catch block
     *
     * @param callable $callback
     * @return JsonResponse
     */
    protected function handleRequest(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
