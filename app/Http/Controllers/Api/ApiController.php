<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * Base response API.
     * 
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $data, int $status, array $headers = []): \Illuminate\Http\JsonResponse
    {
        $headers['Content-Type'] = 'application/json';

        return response()->json($data, $status, $headers, JSON_UNESCAPED_SLASHES);
    }
}
