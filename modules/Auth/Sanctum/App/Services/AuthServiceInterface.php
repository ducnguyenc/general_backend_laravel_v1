<?php

namespace Modules\Auth\Sanctum\App\Services;

interface AuthServiceInterface
{
    public function register(array $request);
}
