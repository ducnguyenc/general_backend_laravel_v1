<?php

namespace App\Services;

use Illuminate\Http\Response;

class BaseService
{
    /**
     * Response success
     *
     * @param  int  $statusCode
     * @param  array  $data
     * @param  string  $message
     * @return array
     */
    public function response($statusCode, $data = [], $message = '')
    {
        $statusArray = [
            1 => 'Informational',
            2 => 'Successful',
            3 => 'Redirection',
            4 => 'Client error',
            5 => 'Server error',
        ];
        $status = $statusArray[floor($statusCode / 100)];

        return [compact('status', 'data', 'message'), $statusCode];
    }
}
