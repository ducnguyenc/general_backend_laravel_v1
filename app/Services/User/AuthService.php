<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthService extends BaseService implements AuthServiceInterface
{
    private $userRepo;

    /**
     * @param  \App\Repositories\UserRepositoryInterface  $userRepo
     */
    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Register user.
     *
     * @param  array  $params
     * @return array
     */
    public function register(array $params): array
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepo->firstOrCreate(
                ['email' => $params['email']],
                [
                    'name' => $params['name'],
                    'email' => $params['email'],
                    'password' => Hash::make($params['password']),
                ]
            );
            event(new Registered($user));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return $this->response(Response::HTTP_INTERNAL_SERVER_ERROR, [], 'Error server.');
        }

        return $this->response(Response::HTTP_OK, []);
    }

    /**
     * Login user.
     *
     * @param  array  $params
     * @return array
     */
    public function login(array $params): array
    {
        if (Auth::attempt($params)) {
            if (!isset(Auth::user()->email_verified_at)) {
                return $this->response(Response::HTTP_UNAUTHORIZED, [], config('messages.error.verify_email'));
            }

            $accessToken = Auth::user()->createToken($params['email'], [User::ABILITY])->plainTextToken;

            return $this->response(Response::HTTP_OK, [
                'access_token' => $accessToken,
            ]);
        }

        return $this->response(Response::HTTP_NOT_FOUND, [], config('messages.error.login'));
    }

    public function forgotPassword(array $params): array
    {
        $user = User::where('email', $params['email'])->first();
        if (!isset($user->email_verified_at)) {
            $this->response(Response::HTTP_OK, [], 'email not verified yet');
        }

        $status = Password::sendResetLink(
            ['email' => $params['email']]
        );

        return $status === Password::RESET_LINK_SENT
            ? $this->response(Response::HTTP_OK, [], $status)
            : $this->response(Response::HTTP_NOT_FOUND, [], $status);
    }

    public function updatePassword(array $params)
    {
        $status = Password::reset(
            $params,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->response(Response::HTTP_OK, [], $status)
            : $this->response(Response::HTTP_NOT_FOUND, [], $status);
    }
}
