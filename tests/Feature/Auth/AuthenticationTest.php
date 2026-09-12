<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Đăng nhập tài khoản');
    }

    public function test_student_with_completed_onboarding_is_redirected_to_student_dashboard(): void
    {
        $user = User::factory()->student()->create([
            'email' => 'student@test.com',
            'password' => 'Password@123',
            'onboarding_completed_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'student@test.com',
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_student_without_onboarding_is_redirected_to_onboarding_screen(): void
    {
        $user = User::factory()->student()->notOnboarded()->create([
            'email' => 'fresh@test.com',
            'password' => 'Password@123',
        ]);

        $response = $this->post('/login', [
            'email' => 'fresh@test.com',
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('onboarding.index'));
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@test.com',
            'password' => 'Password@123',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_banned_user_cannot_login(): void
    {
        User::factory()->banned()->create([
            'email' => 'banned@test.com',
            'password' => 'Password@123',
        ]);

        $response = $this->post('/login', [
            'email' => 'banned@test.com',
            'password' => 'Password@123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
