<?php

namespace Modules\Auth\Sanctum\App\Repositories;

interface AuthRepositoryInterface
{
    public function create(array $attributes);
}
