<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService extends BaseService implements AuthServiceInterface
{
    private $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function register($params)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepo->create([
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => $params['password'],
            ]);
            event(new Registered($user));
            $accessToken = $user->createToken($params['email'], [User::ABILITY])->plainTextToken;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return $this->response(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->response(Response::HTTP_OK, [
            'access_token' => $accessToken,
        ]);
    }
}
