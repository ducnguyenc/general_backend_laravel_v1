<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $uri = 'api/register';

    /**
     * Test success.
     *
     * @return void
     */
    public function test_success(): void
    {
        Notification::fake();
        $password = $this->faker->password;
        $response = $this->postJson($this->uri, [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertStatus(200)->assertJson([
            'status' => 'Successful',
            'data' => [],
            'message' => '',
        ]);
        Notification::assertCount(1);
    }

    /**
     * Test invalid.
     *
     * @dataProvider provideInvalidName
     * @dataProvider provideInvalidEmail
     * @dataProvider provideInvalidPassword
     *
     * @return void
     */
    public function test_invalid($attribute, $value, $message): void
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
     * Provide invalid name.
     *
     * @return array
     */
    public function provideInvalidName()
    {
        return [
            'Name is required' => ['name', null, 'The name field is required.'],
            'Name is limit to 255 chars' => [
                'name',
                str_repeat('a', 256), 'The name must not be greater than 255 characters.',
            ],
        ];
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
            'Email is exists' => ['email', 'valueEmail' => function () {
                User::factory()->create(['email' => 'a@example']);

                return 'a@example';
            }, 'The email has already been taken.'],
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
            'Password is not match confirmation' => [
                'password', str_repeat('a', 8),
                'The password confirmation does not match.',
            ],
        ];
    }
}
