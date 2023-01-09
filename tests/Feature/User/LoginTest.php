<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $uri = 'api/login';

    /**
     * Test success.
     *
     * @return void
     */
    public function test_success()
    {
        $user = User::factory()->create();

        $response = $this->postJson($this->uri, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)->assertJson([
            'status' => 'Successful',
            'data' => [],
            'message' => '',
        ])->assertJsonStructure([
            'status',
            'data' => ['access_token'],
            'message',
        ]);
    }

    /**
     * Test invalid.
     *
     * @dataProvider provideInvalidEmail
     * @dataProvider provideInvalidPassword
     *
     * @return void
     */
    public function test_invalid($attribute, $value, $message)
    {
        $inputs = $this->makeInvalidData([
            $attribute => is_callable($value) ? $value() : $value,
        ]);

        $response = $this->postJson($this->uri, $inputs);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJson([
            'status' => 'Client error',
            'data' => [
                $attribute => [$message],
            ],
            'message' => '',
        ]);
    }

    /**
     * Make invalid data.
     *
     * @param  array  $inputs
     * @return array
     */
    private function makeInvalidData($inputs): array
    {
        return array_filter($inputs, function ($value) {
            return $value !== null;
        });
    }

    /**
     * Provide invalid email.
     *
     * @return array
     */
    public function provideInvalidEmail()
    {
        return [
            'Email is required' => ['email', null, 'The email field is required.'],
            'Email is email address' => ['email', str_repeat('a', 255), 'The email must be a valid email address.'],
            'Email is limit to 255 chars' => [
                'email',
                sprintf('%s@a', str_repeat('a', 256)), 'The email must not be greater than 255 characters.',
            ],
        ];
    }

    /**
     * Provide invalid password.
     *
     * @return array
     */
    public function provideInvalidPassword()
    {
        return [
            'Password is required' => ['password', null, 'The password field is required.'],
            'Password is min 8 chars' => [
                'password', str_repeat('a', 7),
                'The password must be at least 8 characters.',
            ],
            'Password is limit to 255 chars' => [
                'password', str_repeat('a', 256),
                'The password must not be greater than 255 characters.',
            ],
        ];
    }

    /**
     * Test fail email verified at.
     *
     * @return void
     */
    public function test_fail_account()
    {
        $user = User::factory()->create();

        $response = $this->postJson($this->uri, [
            'email' => $this->faker->email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)->assertJson([
            'status' => 'Client error',
            'data' => [],
            'message' => config('messages.error.login'),
        ]);

        $response = $this->postJson($this->uri, [
            'email' => $user->email,
            'password' => $this->faker->password,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)->assertJson([
            'status' => 'Client error',
            'data' => [],
            'message' => config('messages.error.login'),
        ]);
    }

    /**
     * Test fail email verified at.
     *
     * @return void
     */
    public function test_fail_email_verified_at()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->postJson($this->uri, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson([
            'status' => 'Client error',
            'data' => [],
            'message' => config('messages.error.verify_email'),
        ]);
    }
}
