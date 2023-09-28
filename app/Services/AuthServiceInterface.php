<?php

namespace App\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $params, int $role = User::ROLE_USER_V0): array;
    public function login(array $params, int $role = User::ROLE_USER_V0): array;
    public function forgotPassword(array $paramsm, int $role = User::ROLE_USER_V0);
    public function updatePassword(array $params, int $role = User::ROLE_USER_V0);
}
