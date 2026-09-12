<?php

namespace App\Models;

use App\Enums\WordStudyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentVocabularyReview extends Model
{
    use HasFactory;

    protected $table = 'student_vocabulary_reviews';

    protected $fillable = [
        'user_id',
        'vocabulary_item_id',
        'status',
        'mastery_level',
        'is_favorite',
        'personal_note',
        'review_count',
        'correct_count',
        'incorrect_count',
        'last_reviewed_at',
        'next_review_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => WordStudyStatus::class,
            'mastery_level' => 'integer',
            'is_favorite' => 'boolean',
            'review_count' => 'integer',
            'correct_count' => 'integer',
            'incorrect_count' => 'integer',
            'last_reviewed_at' => 'datetime',
            'next_review_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vocabularyItem(): BelongsTo
    {
        return $this->belongsTo(VocabularyItem::class, 'vocabulary_item_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueForReview(Builder $query): Builder
    {
        return $query->where(function (Builder $sub) {
            $sub->where('status', WordStudyStatus::REVIEW_NEEDED->value)
                ->orWhere(function (Builder $q) {
                    $q->whereNotNull('next_review_at')
                      ->where('next_review_at', '<=', now()->toDateString());
                });
        });
    }

    public function scopeFavorites(Builder $query): Builder
    {
        return $query->where('is_favorite', true);
    }
}
