<?php

namespace Tests\Feature\Admin\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Models\User;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_lessons_list(): void
    {
        $admin = User::factory()->admin()->create();
        $topic = VocabularyTopic::factory()->create();
        VocabularyLesson::factory()->create(['topic_id' => $topic->id, 'title' => 'Environmental Protection']);

        $response = $this->actingAs($admin)->get(route('admin.vocabulary.lessons.index'));

        $response->assertStatus(200);
        $response->assertSee('Environmental Protection');
    }

    public function test_admin_can_create_a_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $topic = VocabularyTopic::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.lessons.store'), [
            'topic_id' => $topic->id,
            'title' => 'Renewable Energy',
            'slug' => 'renewable-energy',
            'description' => 'Lesson about solar and wind power',
            'level' => VocabularyLevel::BAND_4_5_5_0->value,
            'estimated_minutes' => 20,
            'sort_order' => 1,
            'status' => ContentStatus::PUBLISHED->value,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.vocabulary.lessons.index', ['topic_id' => $topic->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vocabulary_lessons', [
            'title' => 'Renewable Energy',
            'topic_id' => $topic->id,
            'level' => VocabularyLevel::BAND_4_5_5_0->value,
        ]);
    }

    public function test_admin_can_update_a_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create(['title' => 'Old Lesson']);

        $response = $this->actingAs($admin)->put(route('admin.vocabulary.lessons.update', $lesson), [
            'topic_id' => $lesson->topic_id,
            'title' => 'Updated Lesson Title',
            'slug' => 'updated-lesson-title',
            'description' => 'New Description',
            'level' => VocabularyLevel::BAND_5_5_6_0->value,
            'estimated_minutes' => 25,
            'sort_order' => 2,
            'status' => ContentStatus::PUBLISHED->value,
        ]);

        $response->assertRedirect(route('admin.vocabulary.lessons.index', ['topic_id' => $lesson->topic_id]));

        $lesson->refresh();
        $this->assertEquals('Updated Lesson Title', $lesson->title);
        $this->assertEquals(VocabularyLevel::BAND_5_5_6_0, $lesson->level);
    }

    public function test_admin_can_delete_a_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.vocabulary.lessons.destroy', $lesson));

        $response->assertRedirect(route('admin.vocabulary.lessons.index'));
        $this->assertDatabaseMissing('vocabulary_lessons', ['id' => $lesson->id]);
    }

    public function test_admin_can_preview_a_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create(['title' => 'Lesson To Preview']);

        $response = $this->actingAs($admin)->get(route('admin.vocabulary.lessons.preview', $lesson));

        $response->assertStatus(200);
        $response->assertSee('Lesson To Preview');
        $response->assertSee('Chế độ xem trước');
    }

    public function test_admin_can_toggle_lesson_status(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create(['status' => ContentStatus::DRAFT]);

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.lessons.toggle-status', $lesson));

        $response->assertSessionHas('success');
        $this->assertEquals(ContentStatus::PUBLISHED, $lesson->fresh()->status);
    }
}
