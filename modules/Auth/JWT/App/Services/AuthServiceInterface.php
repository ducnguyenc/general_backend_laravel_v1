<?php

namespace Modules\Auth\JWT\App\Services;

interface AuthServiceInterface
{
    public function register(array $request);
}