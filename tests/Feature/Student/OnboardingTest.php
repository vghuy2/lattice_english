<?php

namespace Tests\Feature\Student;

use App\Enums\IeltsType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_student_is_redirected_to_onboarding_when_accessing_dashboard(): void
    {
        $student = User::factory()->student()->notOnboarded()->create();

        $response = $this->actingAs($student)->get('/student/dashboard');

        $response->assertRedirect(route('onboarding.index'));
    }

    public function test_onboarded_student_is_redirected_to_dashboard_when_accessing_onboarding(): void
    {
        $student = User::factory()->student()->create([
            'onboarding_completed_at' => now(),
        ]);

        $response = $this->actingAs($student)->get('/onboarding');

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_student_can_complete_onboarding(): void
    {
        $student = User::factory()->student()->notOnboarded()->create();

        $response = $this->actingAs($student)->post('/onboarding', [
            'current_band' => 4.5,
            'target_band' => 6.5,
            'test_type' => IeltsType::ACADEMIC->value,
            'target_date' => now()->addMonths(4)->format('Y-m-d'),
            'study_days_per_week' => 5,
            'study_goal' => 'Tập trung từ vựng Academic và cấu trúc bài Task 2',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertTrue($student->hasCompletedOnboarding());
        $this->assertEquals(4.5, $student->current_band);
        $this->assertEquals(6.5, $student->target_band);
        $this->assertEquals(IeltsType::ACADEMIC, $student->test_type);
        $this->assertEquals(5, $student->study_days_per_week);
    }

    public function test_onboarding_validation_fails_if_target_band_is_lower_than_current_band(): void
    {
        $student = User::factory()->student()->notOnboarded()->create();

        $response = $this->actingAs($student)->post('/onboarding', [
            'current_band' => 6.0,
            'target_band' => 5.0, // Invalid: target lower than current
            'test_type' => IeltsType::ACADEMIC->value,
            'study_days_per_week' => 5,
        ]);

        $response->assertSessionHasErrors('target_band');
        $this->assertFalse($student->fresh()->hasCompletedOnboarding());
    }
}
