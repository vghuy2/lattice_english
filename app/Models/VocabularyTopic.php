<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VocabularyTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'icon',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'sort_order' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->title);
            }
        });
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(VocabularyLesson::class, 'topic_id');
    }

    public function publishedLessons(): HasMany
    {
        return $this->hasMany(VocabularyLesson::class, 'topic_id')
            ->where('status', ContentStatus::PUBLISHED->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(VocabularyItem::class, VocabularyLesson::class, 'topic_id', 'lesson_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        return null;
    }

    public function isPublished(): bool
    {
        return $this->status === ContentStatus::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === ContentStatus::DRAFT;
    }

    public function isArchived(): bool
    {
        return $this->status === ContentStatus::ARCHIVED;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::PUBLISHED->value);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('title', 'asc');
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
