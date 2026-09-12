<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WritingPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_type',
        'prompt_type',
        'topic_id',
        'title',
        'slug',
        'prompt_text',
        'image_path',
        'level',
        'min_words',
        'time_limit_minutes',
        'guidance',
        'suggested_outline',
        'status',
        'published_at',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'task_type' => WritingTaskType::class,
            'prompt_type' => WritingPromptType::class,
            'level' => VocabularyLevel::class,
            'status' => ContentStatus::class,
            'suggested_outline' => 'array',
            'published_at' => 'datetime',
            'min_words' => 'integer',
            'time_limit_minutes' => 'integer',
            'order_index' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (WritingPrompt $prompt) {
            if (empty($prompt->slug)) {
                $prompt->slug = Str::slug($prompt->title);
            }
        });
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(VocabularyTopic::class, 'topic_id');
    }

    public function sampleEssays(): HasMany
    {
        return $this->hasMany(SampleEssay::class, 'writing_prompt_id')->orderBy('band_score', 'asc');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
                return $this->image_path;
            }

            if (Storage::disk('public')->exists($this->image_path)) {
                return Storage::disk('public')->url($this->image_path);
            }
        }

        return null;
    }

    public function isTask1(): bool
    {
        return $this->task_type === WritingTaskType::TASK_1;
    }

    public function isTask2(): bool
    {
        return $this->task_type === WritingTaskType::TASK_2;
    }

    public function isAvailableForStudents(): bool
    {
        return $this->status === ContentStatus::PUBLISHED && ($this->published_at === null || $this->published_at->isPast());
    }

    public function scopeAvailableForStudents(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::PUBLISHED->value)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeTask1(Builder $query): Builder
    {
        return $query->where('task_type', WritingTaskType::TASK_1->value);
    }

    public function scopeTask2(Builder $query): Builder
    {
        return $query->where('task_type', WritingTaskType::TASK_2->value);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_index', 'asc')->orderBy('id', 'desc');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('prompt_text', 'like', "%{$term}%");
        });
    }
}
