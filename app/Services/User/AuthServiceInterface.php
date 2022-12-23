<?php

namespace App\Services\User;

interface AuthServiceInterface
{
    public function register(array $params): array;
    public function login(array $params);
    public function forgotPassword(array $params);
    public function updatePassword(array $params);
}
