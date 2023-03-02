<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
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
}
