<?php

namespace Tests\Feature\Admin\Writing;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_admin_writing_routes(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)
            ->get(route('admin.writing.prompts.index'));

        $response->assertForbidden();

        $resScoring = $this->actingAs($student)
            ->get(route('admin.writing.scoring.index'));

        $resScoring->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_admin_writing(): void
    {
        $response = $this->get(route('admin.writing.prompts.index'));
        $response->assertRedirect(route('login'));
    }
}
