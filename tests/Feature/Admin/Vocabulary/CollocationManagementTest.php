<?php

namespace Tests\Feature\Admin\Vocabulary;

use App\Enums\CollocationType;
use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Models\Collocation;
use App\Models\User;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollocationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_collocations_management(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('admin.vocabulary.collocations.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_collocations_list(): void
    {
        $admin = User::factory()->admin()->create();
        $topic = VocabularyTopic::factory()->create(['title' => 'Global Warming']);

        Collocation::factory()->create([
            'topic_id' => $topic->id,
            'phrase' => 'cut carbon emissions',
            'meaning' => 'cắt giảm lượng khí thải carbon',
            'type' => CollocationType::VERB_NOUN,
            'level' => VocabularyLevel::BAND_6_5_PLUS,
            'status' => ContentStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.vocabulary.collocations.index'));

        $response->assertStatus(200);
        $response->assertSee('cut carbon emissions');
        $response->assertSee('cắt giảm lượng khí thải carbon');
        $response->assertSee('Global Warming');
    }

    public function test_admin_can_create_a_collocation(): void
    {
        $admin = User::factory()->admin()->create();
        $topic = VocabularyTopic::factory()->create(['title' => 'Higher Education']);

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.collocations.store'), [
            'topic_id' => $topic->id,
            'phrase' => 'play an indispensable role in',
            'meaning' => 'đóng một vai trò không thể thiếu trong...',
            'type' => CollocationType::VERB_NOUN->value,
            'level' => VocabularyLevel::BAND_6_5_PLUS->value,
            'example_sentence' => 'Tertiary education plays an indispensable role in societal advancement.',
            'example_sentence_vi' => 'Giáo dục đại học đóng một vai trò không thể thiếu trong sự tiến bộ của xã hội.',
            'writing_notes' => 'Thường dùng trong mở bài hoặc kết bài IELTS Task 2.',
            'status' => ContentStatus::PUBLISHED->value,
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.vocabulary.collocations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('collocations', [
            'phrase' => 'play an indispensable role in',
            'meaning' => 'đóng một vai trò không thể thiếu trong...',
            'topic_id' => $topic->id,
            'type' => CollocationType::VERB_NOUN->value,
            'status' => ContentStatus::PUBLISHED->value,
        ]);
    }

    public function test_admin_can_update_a_collocation(): void
    {
        $admin = User::factory()->admin()->create();
        $collocation = Collocation::factory()->create([
            'phrase' => 'foster economy',
            'meaning' => 'phát triển kinh tế',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.vocabulary.collocations.update', $collocation), [
            'phrase' => 'foster economic prosperity',
            'meaning' => 'thúc đẩy sự thịnh vượng kinh tế',
            'type' => CollocationType::VERB_NOUN->value,
            'level' => VocabularyLevel::BAND_6_5_PLUS->value,
            'status' => ContentStatus::PUBLISHED->value,
            'sort_order' => 2,
        ]);

        $response->assertRedirect(route('admin.vocabulary.collocations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('collocations', [
            'id' => $collocation->id,
            'phrase' => 'foster economic prosperity',
            'meaning' => 'thúc đẩy sự thịnh vượng kinh tế',
        ]);
    }

    public function test_admin_can_delete_a_collocation(): void
    {
        $admin = User::factory()->admin()->create();
        $collocation = Collocation::factory()->create(['phrase' => 'obsolete collocation']);

        $response = $this->actingAs($admin)->delete(route('admin.vocabulary.collocations.destroy', $collocation));

        $response->assertRedirect(route('admin.vocabulary.collocations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('collocations', [
            'id' => $collocation->id,
        ]);
    }

    public function test_admin_can_toggle_collocation_status(): void
    {
        $admin = User::factory()->admin()->create();
        $collocation = Collocation::factory()->create(['status' => ContentStatus::PUBLISHED]);

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.collocations.toggle-status', $collocation));

        $response->assertRedirect();
        $this->assertEquals(ContentStatus::DRAFT, $collocation->fresh()->status);

        $this->actingAs($admin)->post(route('admin.vocabulary.collocations.toggle-status', $collocation));
        $this->assertEquals(ContentStatus::PUBLISHED, $collocation->fresh()->status);
    }
}
