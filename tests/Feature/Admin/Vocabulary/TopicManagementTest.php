<?php

namespace Tests\Feature\Admin\Vocabulary;

use App\Enums\ContentStatus;
use App\Models\User;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_topics_management(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('admin.vocabulary.topics.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_topics_list(): void
    {
        $admin = User::factory()->admin()->create();
        VocabularyTopic::factory()->create(['title' => 'Technology Innovation', 'status' => ContentStatus::PUBLISHED]);

        $response = $this->actingAs($admin)->get(route('admin.vocabulary.topics.index'));

        $response->assertStatus(200);
        $response->assertSee('Technology Innovation');
    }

    public function test_admin_can_create_a_new_topic(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.topics.store'), [
            'title' => 'Health and Wellness',
            'slug' => 'health-and-wellness',
            'description' => 'Vocabulary for health topics',
            'icon' => '🩺',
            'sort_order' => 5,
            'status' => ContentStatus::PUBLISHED->value,
        ]);

        $response->assertRedirect(route('admin.vocabulary.topics.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vocabulary_topics', [
            'title' => 'Health and Wellness',
            'slug' => 'health-and-wellness',
            'status' => ContentStatus::PUBLISHED->value,
        ]);
    }

    public function test_admin_can_update_a_topic(): void
    {
        $admin = User::factory()->admin()->create();
        $topic = VocabularyTopic::factory()->create(['title' => 'Old Title']);

        $response = $this->actingAs($admin)->put(route('admin.vocabulary.topics.update', $topic), [
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'description' => 'Updated Description',
            'status' => ContentStatus::PUBLISHED->value,
        ]);

        $response->assertRedirect(route('admin.vocabulary.topics.index'));

        $topic->refresh();
        $this->assertEquals('Updated Title', $topic->title);
    }

    public function test_admin_can_delete_a_topic(): void
    {
        $admin = User::factory()->admin()->create();
        $topic = VocabularyTopic::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.vocabulary.topics.destroy', $topic));

        $response->assertRedirect(route('admin.vocabulary.topics.index'));
        $this->assertDatabaseMissing('vocabulary_topics', ['id' => $topic->id]);
    }

    public function test_admin_can_toggle_topic_status(): void
    {
        $admin = User::factory()->admin()->create();
        $topic = VocabularyTopic::factory()->create(['status' => ContentStatus::DRAFT]);

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.topics.toggle-status', $topic));

        $response->assertSessionHas('success');
        $this->assertEquals(ContentStatus::PUBLISHED, $topic->fresh()->status);
    }
}
