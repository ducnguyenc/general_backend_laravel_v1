<?php

namespace Modules\Auth\JWT\App\Http\Controller;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Modules\Auth\JWT\App\Http\Requests\LoginRequest;
use Modules\Auth\JWT\App\Http\Requests\RegisterRequest;
use Modules\Auth\JWT\App\Services\AuthServiceInterface;

class AuthController extends BaseController
{
    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        [$status, $data] = $this->authService->register($request->validated());

        return $this->response($status, $data);
    }

    /**
     * Login user.
     */
    public function login(LoginRequest $request)
    {
        [$status, $data] = $this->authService->login($request->validated());

        return $this->response($status, $data);
    }
}
