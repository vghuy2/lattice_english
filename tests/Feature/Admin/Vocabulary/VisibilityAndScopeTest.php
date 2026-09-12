<?php

namespace Tests\Feature\Admin\Vocabulary;

use App\Enums\ContentStatus;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisibilityAndScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_for_students_scope_only_includes_published_past_lessons(): void
    {
        $topic = VocabularyTopic::factory()->create(['status' => ContentStatus::PUBLISHED]);

        // 1. Published lesson with past date -> SHOULD be included
        $activeLesson = VocabularyLesson::factory()->create([
            'topic_id' => $topic->id,
            'title' => 'Available Lesson',
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subHour(),
        ]);

        // 2. Draft lesson -> SHOULD NOT be included
        $draftLesson = VocabularyLesson::factory()->create([
            'topic_id' => $topic->id,
            'title' => 'Draft Lesson',
            'status' => ContentStatus::DRAFT,
            'published_at' => now()->subHour(),
        ]);

        // 3. Published lesson with future published_at -> SHOULD NOT be included
        $futureLesson = VocabularyLesson::factory()->create([
            'topic_id' => $topic->id,
            'title' => 'Future Scheduled Lesson',
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->addDays(3),
        ]);

        $availableLessons = VocabularyLesson::availableForStudents()->get();

        $this->assertTrue($availableLessons->contains('id', $activeLesson->id));
        $this->assertFalse($availableLessons->contains('id', $draftLesson->id));
        $this->assertFalse($availableLessons->contains('id', $futureLesson->id));
    }

    public function test_lesson_is_available_for_students_helper_method(): void
    {
        $topic = VocabularyTopic::factory()->create();

        $activeLesson = VocabularyLesson::factory()->create([
            'topic_id' => $topic->id,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subMinutes(10),
        ]);

        $futureLesson = VocabularyLesson::factory()->create([
            'topic_id' => $topic->id,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->addMinutes(10),
        ]);

        $draftLesson = VocabularyLesson::factory()->create([
            'topic_id' => $topic->id,
            'status' => ContentStatus::DRAFT,
            'published_at' => now()->subMinutes(10),
        ]);

        $this->assertTrue($activeLesson->isAvailableForStudents());
        $this->assertFalse($futureLesson->isAvailableForStudents());
        $this->assertFalse($draftLesson->isAvailableForStudents());
    }
}
