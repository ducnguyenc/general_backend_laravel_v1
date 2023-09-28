<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
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
    public function register(array $params, int $role = User::ROLE_USER_V0): array
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepo->firstOrCreate(
                ['email' => $params['email']],
                [
                    'name' => $params['name'],
                    'email' => $params['email'],
                    'password' => Hash::make($params['password']),
                    'role' => $role ?? User::ROLE_USER_V0,
                ]
            );
            // event(new Registered($user));

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
    public function login(array $params, int $role = User::ROLE_USER_V0): array
    {
        $params['role'] = $role;
        if (Auth::attempt($params)) {
            if (!isset(Auth::user()->email_verified_at)) {
                return $this->response(Response::HTTP_UNAUTHORIZED, [], config('messages.error.verify_email'));
            }

            $accessToken = Auth::user()->createToken($params['email'], [User::ABILITY_USER])->plainTextToken;

            return $this->response(Response::HTTP_OK, [
                'access_token' => $accessToken,
            ]);
        }

        return $this->response(Response::HTTP_NOT_FOUND, [], config('messages.error.login'));
    }

    /**
     * Forgot password.
     *
     * @param  array  $params
     * @param  int  $role
     * @return array
     */
    public function forgotPassword(array $params, int $role = User::ROLE_USER_V0): array
    {
        $user = User::query()->where([
            'email' => $params['email'],
            'role' => $role,
        ])->first();
        if (!isset($user->email_verified_at)) {
            $this->response(Response::HTTP_OK, [], 'email not verified yet');
        }

        $status = Password::sendResetLink([
            'email' => $params['email'],
            'role' => $role,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? $this->response(Response::HTTP_OK, [], $status)
            : $this->response(Response::HTTP_NOT_FOUND, [], $status);
    }

    /**
     * Update password.
     *
     * @param  array  $params
     * @param  int  $role
     * @return array
     */
    public function updatePassword(array $params, int $role = User::ROLE_USER_V0): array
    {
        $params['role'] = $role;
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
