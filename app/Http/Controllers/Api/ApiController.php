<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function response($data, $status, $headers = [])
    {
        $headers['Content-Type'] = 'application/json';

        return response()->json($data, $status, $headers, JSON_UNESCAPED_SLASHES);
    }
}
