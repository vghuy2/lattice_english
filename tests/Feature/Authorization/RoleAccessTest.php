<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_student_dashboard(): void
    {
        $response = $this->get('/student/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_student_dashboard_directly(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/student/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Tổng quan Hệ thống Quản trị');
    }

    public function test_student_can_access_student_dashboard(): void
    {
        $student = User::factory()->student()->create([
            'onboarding_completed_at' => now(),
        ]);

        $response = $this->actingAs($student)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }
}
