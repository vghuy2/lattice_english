<?php

namespace App\Models;

use App\Enums\PartOfSpeech;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VocabularyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'word',
        'vietnamese_meaning',
        'part_of_speech',
        'ipa',
        'audio',
        'example_sentence',
        'example_sentence_vi',
        'collocations',
        'synonyms',
        'antonyms',
        'writing_notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'part_of_speech' => PartOfSpeech::class,
            'collocations' => 'array',
            'synonyms' => 'array',
            'antonyms' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(VocabularyLesson::class, 'lesson_id');
    }

    public function getAudioUrlAttribute(): ?string
    {
        if ($this->audio) {
            if (str_starts_with($this->audio, 'http://') || str_starts_with($this->audio, 'https://')) {
                return $this->audio;
            }

            if (Storage::disk('public')->exists($this->audio)) {
                return Storage::disk('public')->url($this->audio);
            }
        }

        return null;
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('word', 'like', "%{$term}%")
              ->orWhere('vietnamese_meaning', 'like', "%{$term}%")
              ->orWhere('example_sentence', 'like', "%{$term}%");
        });
    }
}
