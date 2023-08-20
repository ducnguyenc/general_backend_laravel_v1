<?php

namespace Modules\Auth\JWT\App\Repositories;

use Modules\Auth\JWT\App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    private $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }
}
