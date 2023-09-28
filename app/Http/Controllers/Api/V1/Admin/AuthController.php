<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Services\AuthServiceInterface;
use Illuminate\Http\JsonResponse;

class AuthController extends ApiController
{
    private $authService;

    /**
     * @param  \App\Services\AuthServiceInterface  $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register user.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        [$data, $status] = $this->authService->register($request->validated(), User::ROLE_ADMIN);

        return $this->response($data, $status);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        [$data, $status] = $this->authService->login($request->validated(), User::ROLE_ADMIN);

        return $this->response($data, $status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Http\Requests\EmailVerificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->to(env('APP_URL_FE') . config('const.uri_fe.login'));
    }

    /**
     * Forgot password.
     *
     * @param  \App\Http\Requests\ForgotPasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        [$data, $status] = $this->authService->forgotPassword($request->validated(), User::ROLE_ADMIN);

        return $this->response($data, $status);
    }

    /**
     * Reset password.
     *
     * @param  string  $token
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function resetPassword(string $token)
    {
        return redirect(sprintf('%s%s/%s', env('APP_URL_FE'), config('const.uri_fe.reset-password'), $token));
    }

    /**
     * Update password reset.
     *
     * @param  \App\Http\Requests\UpdatePasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        [$data, $status] = $this->authService->updatePassword(
            $request->only('email', 'password', 'password_confirmation', 'token'), User::ROLE_ADMIN
        );

        return $this->response($data, $status);
    }
}
