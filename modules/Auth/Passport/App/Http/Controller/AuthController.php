<?php

namespace Modules\Auth\Passport\App\Http\Controller;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Passport\App\Http\Requests\RegisterRequest;
use Modules\Auth\Passport\App\Services\AuthServiceInterface;

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
}
