<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait providing response methods for controllers.
 */
trait Responsable
{
    /**
     * Return a success response.
     *
     * @param mixed $data The data to include in the response.
     * @param string $message Optional message to include in the response.
     * @param int $status Optional HTTP status code.
     *
     * @return JsonResponse The JSON response.
     */
    protected function success(mixed $data, string $message = "", int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ], $status);
    }

    /**
     * Return an error response.
     *
     * @param mixed $data The error data to include in the response.
     * @param int $status The HTTP status code.
     *
     * @return JsonResponse The JSON response.
     */
    protected function error(mixed $data, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $data
        ], $status);
    }
}
