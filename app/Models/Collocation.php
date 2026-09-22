<?php

namespace App\Models;

use App\Enums\CollocationType;
use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'phrase',
        'meaning',
        'type',
        'level',
        'example_sentence',
        'example_sentence_vi',
        'writing_notes',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => CollocationType::class,
            'level' => VocabularyLevel::class,
            'status' => ContentStatus::class,
            'sort_order' => 'integer',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(VocabularyTopic::class, 'topic_id');
    }

    public function isPublished(): bool
    {
        return $this->status === ContentStatus::PUBLISHED;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::PUBLISHED->value);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('phrase', 'like', "%{$term}%")
              ->orWhere('meaning', 'like', "%{$term}%")
              ->orWhere('example_sentence', 'like', "%{$term}%")
              ->orWhere('writing_notes', 'like', "%{$term}%");
        });
    }
}
