<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Services\User\AuthServiceInterface;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends ApiController
{
    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
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

        if ($status !== Response::HTTP_OK) {
            return $this->response($data, $status);
        }

        if (!request()->hasValidSignature()) {
            return $this->response(['message' => 'validate signature'], Response::HTTP_FOUND);
        }

        return $this->response($data, $status);
    }

    /**
     * Show the form for creating a new resource.
     *
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
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        $this->response(['status' => 'success', 'data' => [], 'message' => ''], Response::HTTP_OK);
    }
}
