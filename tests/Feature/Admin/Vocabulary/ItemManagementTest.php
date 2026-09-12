<?php

namespace Tests\Feature\Admin\Vocabulary;

use App\Enums\PartOfSpeech;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_items_list_of_a_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create();
        VocabularyItem::factory()->create(['lesson_id' => $lesson->id, 'word' => 'curriculum']);

        $response = $this->actingAs($admin)->get(route('admin.vocabulary.lessons.items.index', $lesson));

        $response->assertStatus(200);
        $response->assertSee('curriculum');
    }

    public function test_admin_can_add_vocabulary_item(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.lessons.items.store', $lesson), [
            'lesson_id' => $lesson->id,
            'word' => 'sustainable',
            'vietnamese_meaning' => 'bền vững',
            'part_of_speech' => PartOfSpeech::ADJECTIVE->value,
            'ipa' => '/səˈsteɪ.nə.bəl/',
            'example_sentence' => 'Sustainable development is crucial for environmental preservation.',
            'example_sentence_vi' => 'Phát triển bền vững là điều tối quan trọng để bảo tồn môi trường.',
            'collocations' => 'sustainable development, sustainable growth',
            'synonyms' => 'renewable, eco-friendly',
            'antonyms' => 'unsustainable',
            'writing_notes' => 'Rất hay dùng trong Task 2 chủ đề Môi trường và Kinh tế.',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.vocabulary.lessons.items.index', $lesson));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vocabulary_items', [
            'lesson_id' => $lesson->id,
            'word' => 'sustainable',
            'part_of_speech' => PartOfSpeech::ADJECTIVE->value,
        ]);

        $item = VocabularyItem::where('word', 'sustainable')->first();
        $this->assertIsArray($item->collocations);
        $this->assertContains('sustainable development', $item->collocations);
    }

    public function test_admin_can_update_vocabulary_item(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create();
        $item = VocabularyItem::factory()->create(['lesson_id' => $lesson->id, 'word' => 'oldword']);

        $response = $this->actingAs($admin)->put(route('admin.vocabulary.lessons.items.update', [$lesson, $item]), [
            'lesson_id' => $lesson->id,
            'word' => 'newword',
            'vietnamese_meaning' => 'nghĩa mới',
            'part_of_speech' => PartOfSpeech::VERB->value,
            'example_sentence' => 'This is a new example sentence for IELTS writing.',
            'sort_order' => 2,
        ]);

        $response->assertRedirect(route('admin.vocabulary.lessons.items.index', $lesson));

        $item->refresh();
        $this->assertEquals('newword', $item->word);
        $this->assertEquals(PartOfSpeech::VERB, $item->part_of_speech);
    }

    public function test_admin_can_delete_vocabulary_item(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create();
        $item = VocabularyItem::factory()->create(['lesson_id' => $lesson->id]);

        $response = $this->actingAs($admin)->delete(route('admin.vocabulary.lessons.items.destroy', [$lesson, $item]));

        $response->assertRedirect(route('admin.vocabulary.lessons.items.index', $lesson));
        $this->assertDatabaseMissing('vocabulary_items', ['id' => $item->id]);
    }

    public function test_admin_can_reorder_vocabulary_items(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = VocabularyLesson::factory()->create();
        $item1 = VocabularyItem::factory()->create(['lesson_id' => $lesson->id, 'sort_order' => 1]);
        $item2 = VocabularyItem::factory()->create(['lesson_id' => $lesson->id, 'sort_order' => 2]);

        $response = $this->actingAs($admin)->post(route('admin.vocabulary.lessons.items.reorder', $lesson), [
            'orders' => [
                1 => $item2->id,
                2 => $item1->id,
            ],
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(1, $item2->fresh()->sort_order);
        $this->assertEquals(2, $item1->fresh()->sort_order);
    }
}
