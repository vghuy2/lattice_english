<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Tạo tài khoản học viên');
    }

    public function test_new_users_can_register_as_student(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nguyễn Văn Test',
            'email' => 'testuser@example.com',
            'password' => 'Password@123456',
            'password_confirmation' => 'Password@123456',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::STUDENT, $user->role);
        $this->assertFalse($user->hasCompletedOnboarding());

        $response->assertRedirect(route('onboarding.index'));
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Duplicate User',
            'email' => 'existing@example.com',
            'password' => 'Password@123456',
            'password_confirmation' => 'Password@123456',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
