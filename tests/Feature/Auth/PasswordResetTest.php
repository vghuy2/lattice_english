<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Quên mật khẩu?');
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        $user = User::factory()->create(['email' => 'resetme@example.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'resetme@example.com',
        ]);

        $response->assertSessionHas('status');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'resetuser@example.com']);
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'resetuser@example.com',
            'password' => 'NewSecretPassword123',
            'password_confirmation' => 'NewSecretPassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewSecretPassword123', $user->fresh()->password));
    }
}
