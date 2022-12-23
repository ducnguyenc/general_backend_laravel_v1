<?php

namespace Tests\Feature\User;

use App\Http\Controllers\Api\V1\User\AuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $uri = '/api/register';

    /**
     * Test success.
     *
     * @return void
     */
    public function test_success(): void
    {
        $password = $this->faker->password;
        $response = $this->postJson($this->uri, [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test invalid.
     * 
     * @dataProvider provideInvalidName
     *
     * @return void
     */
    public function test_invalid($attribute, $value, $message): void
    {
        $inputs = $this->makeInvalidData([
            $attribute => $value,
        ]);

        $response = $this->postJson($this->uri, $inputs);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => 'Client error',
                'data' => [
                    $attribute => $message,
                ],
                'message' => '',
            ]);
    }

    /**
     * Make invalid data.
     * 
     * @param array $inputs
     * @return array
     */
    function makeInvalidData($inputs): array
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
    function provideInvalidName()
    {
        return [
            'Name is required' => ['name', null, trans('validation.required', ['attribute' => 'The email field is required.'])],
            'Name is limit to 255 chars' => ['name', str_repeat('a', 256)],
        ];
    }
}
