<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\EmailVerificationRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Services\User\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends ApiController
{
    private $authService;

    /**
     * @param  \App\Services\User\AuthServiceInterface  $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register user.
     *
     * @param  \App\Http\Requests\User\RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        [$data, $status] = $this->authService->register($request->validated());

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
        [$data, $status] = $this->authService->login($request->validated());

        return $this->response($data, $status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  App\Http\Requests\User\EmailVerificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->to(env('APP_URL_FE') . config('const.uri_fe.login'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        $this->response(['status' => 'success', 'data' => [], 'message' => ''], Response::HTTP_OK);
    }

    /**
     * Forgot password.
     *
     * @param  App\Http\Requests\User\ForgotPasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        [$data, $status] = $this->authService->forgotPassword($request->validated());

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
     * @param  App\Http\Requests\User\UpdatePasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        [$data, $status] = $this->authService->updatePassword(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );

        return $this->response($data, $status);
    }
}
