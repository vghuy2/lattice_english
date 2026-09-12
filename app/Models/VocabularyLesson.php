<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VocabularyLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'title',
        'slug',
        'description',
        'level',
        'thumbnail',
        'estimated_minutes',
        'sort_order',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'level' => VocabularyLevel::class,
            'estimated_minutes' => 'integer',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(VocabularyTopic::class, 'topic_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VocabularyItem::class, 'lesson_id')->orderBy('sort_order', 'asc');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail && Storage::disk('public')->exists($this->thumbnail)) {
            return Storage::disk('public')->url($this->thumbnail);
        }

        return null;
    }

    public function isAvailableForStudents(): bool
    {
        return $this->status === ContentStatus::PUBLISHED
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::PUBLISHED->value);
    }

    public function scopeAvailableForStudents(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::PUBLISHED->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByLevel(Builder $query, ?string $level): Builder
    {
        if (empty($level)) {
            return $query;
        }

        return $query->where('level', $level);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
