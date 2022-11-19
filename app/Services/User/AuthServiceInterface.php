<?php

namespace App\Services\User;

interface AuthServiceInterface
{
    public function register($params);
    public function login($params);
}
