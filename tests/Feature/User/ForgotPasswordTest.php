<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use WithFaker;

    private $uri = 'api/forgot-password';

    /**
     * Test success.
     *
     * @return void
     */
    public function test_success()
    {
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->postJson($this->uri, [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        Notification::assertCount(1);
    }

    /**
     * Test fail email.
     *
     * @return void
     */
    public function test_fail_email()
    {
        $response = $this->postJson($this->uri, [
            'email' => $this->faker->email,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)->assertJson([
            'status' => 'Client error',
            'data' => [],
            'message' => PasswordBroker::INVALID_USER,
        ]);
    }
}
