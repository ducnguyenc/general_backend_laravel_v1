<?php

namespace Modules\Auth\JWT\App\Services;

use Illuminate\Http\Response;
use Modules\Auth\JWT\App\Repositories\AuthRepositoryInterface;

class AuthService implements AuthServiceInterface
{
    private $authRepo;

    public function __construct(AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function register(array $request)
    {
        $request['password'] = bcrypt($request['password']);
        $user = $this->authRepo->create($request);

        return [
            Response::HTTP_OK,
            [
                'status' => 'success',
                'data' => compact('user'),
            ]
        ];
    }

    public function login(array $request)
    {
        if (!$token = auth('api')->attempt($request)) {
            return [
                Response::HTTP_OK,
                [
                    'status' => 'success',
                    'data' => compact('token'),
                ]
            ];
        }

        return [
            Response::HTTP_OK,
            [
                'status' => 'success',
                'data' => compact('token'),
            ]
        ];
    }
}
