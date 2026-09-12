<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VocabularyService
{
    /**
     * Parse comma-separated or multi-line string into a clean array.
     */
    public function parseListInput(string|array|null $input): array
    {
        if (empty($input)) {
            return [];
        }

        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }

        // Split by newline or comma or semicolon
        $items = preg_split('/[\r\n,;]+/', $input);

        return array_values(array_filter(array_map('trim', $items)));
    }

    /**
     * Create or update Topic.
     */
    public function saveTopic(array $data, ?VocabularyTopic $topic = null, ?UploadedFile $image = null): VocabularyTopic
    {
        return DB::transaction(function () use ($data, $topic, $image) {
            $topic = $topic ?? new VocabularyTopic();

            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Ensure unique slug
            $originalSlug = $data['slug'];
            $count = 1;
            while (VocabularyTopic::where('slug', $data['slug'])->where('id', '!=', $topic->id ?? 0)->exists()) {
                $data['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }

            if ($image) {
                if ($topic->image && Storage::disk('public')->exists($topic->image)) {
                    Storage::disk('public')->delete($topic->image);
                }
                $data['image'] = $image->store('topics', 'public');
            }

            $topic->fill($data);
            $topic->save();

            return $topic;
        });
    }

    /**
     * Create or update Lesson.
     */
    public function saveLesson(array $data, ?VocabularyLesson $lesson = null, ?UploadedFile $thumbnail = null): VocabularyLesson
    {
        return DB::transaction(function () use ($data, $lesson, $thumbnail) {
            $lesson = $lesson ?? new VocabularyLesson();

            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $originalSlug = $data['slug'];
            $count = 1;
            while (VocabularyLesson::where('slug', $data['slug'])->where('id', '!=', $lesson->id ?? 0)->exists()) {
                $data['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }

            if ($thumbnail) {
                if ($lesson->thumbnail && Storage::disk('public')->exists($lesson->thumbnail)) {
                    Storage::disk('public')->delete($lesson->thumbnail);
                }
                $data['thumbnail'] = $thumbnail->store('lessons', 'public');
            }

            // If status is published and published_at is not set, default to now
            if (($data['status'] ?? null) === ContentStatus::PUBLISHED->value && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $lesson->fill($data);
            $lesson->save();

            return $lesson;
        });
    }

    /**
     * Create or update Vocabulary Item.
     */
    public function saveItem(array $data, ?VocabularyItem $item = null, ?UploadedFile $audio = null): VocabularyItem
    {
        return DB::transaction(function () use ($data, $item, $audio) {
            $item = $item ?? new VocabularyItem();

            if (isset($data['collocations'])) {
                $data['collocations'] = $this->parseListInput($data['collocations']);
            }

            if (isset($data['synonyms'])) {
                $data['synonyms'] = $this->parseListInput($data['synonyms']);
            }

            if (isset($data['antonyms'])) {
                $data['antonyms'] = $this->parseListInput($data['antonyms']);
            }

            if ($audio) {
                if ($item->audio && ! str_starts_with($item->audio, 'http') && Storage::disk('public')->exists($item->audio)) {
                    Storage::disk('public')->delete($item->audio);
                }
                $data['audio'] = $audio->store('audio/vocab', 'public');
            }

            $item->fill($data);
            $item->save();

            return $item;
        });
    }
}
