<?php

namespace Tests\Feature\Profile;

use App\Enums\IeltsType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Cài đặt Hồ sơ cá nhân');
    }

    public function test_user_can_update_basic_profile_info(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)->put('/profile/info', [
            'name' => 'New Name Updated',
            'email' => 'new@example.com',
        ]);

        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('New Name Updated', $user->name);
        $this->assertEquals('new@example.com', $user->email);
    }

    public function test_student_can_update_ielts_goals(): void
    {
        $student = User::factory()->student()->create([
            'current_band' => 4.5,
            'target_band' => 6.0,
        ]);

        $response = $this->actingAs($student)->put('/profile/goals', [
            'current_band' => 5.0,
            'target_band' => 7.0,
            'test_type' => IeltsType::ACADEMIC->value,
            'target_date' => now()->addMonths(6)->format('Y-m-d'),
            'study_days_per_week' => 6,
            'study_goal' => 'Cập nhật mục tiêu Band 7.0',
        ]);

        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertEquals(5.0, $student->current_band);
        $this->assertEquals(7.0, $student->target_band);
        $this->assertEquals(6, $student->study_days_per_week);
        $this->assertEquals('Cập nhật mục tiêu Band 7.0', $student->study_goal);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewPassword456!', $user->fresh()->password));
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'WrongPassword123!',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertFalse(Hash::check('NewPassword456!', $user->fresh()->password));
    }

    public function test_user_can_upload_and_delete_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);

        // Test delete avatar
        $deleteResponse = $this->actingAs($user)->delete('/profile/avatar');
        $deleteResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->avatar);
    }
}
