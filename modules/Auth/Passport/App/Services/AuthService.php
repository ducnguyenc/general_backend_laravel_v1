<?php

namespace Modules\Auth\Passport\App\Services;

use Modules\Auth\Passport\App\Repositories\AuthRepositoryInterface;

class AuthService implements AuthServiceInterface
{
    private $authRepo;

    public function __construct(AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function register(array $request)
    {
        return [
            200,
            [
                'status' => 'success',
                'data' =>  $this->authRepo->create($request)
            ]
        ];
    }
}
