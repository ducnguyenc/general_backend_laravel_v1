<?php

namespace Modules\Auth\JWT\App\Repositories;

interface AuthRepositoryInterface
{
    public function create(array $attributes);
}
