<?php

namespace App\Http\Controllers;

use \Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Base response API.
     */
    public function response(int $status, array $data, array $headers = []): JsonResponse
    {
        $headers['Content-Type'] = 'application/json';

        return response()->json($data, $status, $headers, JSON_UNESCAPED_SLASHES);
    }
}
