<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocabularyPracticeSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'session_type',
        'total_questions',
        'correct_answers',
        'accuracy_rate',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_questions' => 'integer',
            'correct_answers' => 'integer',
            'accuracy_rate' => 'float',
            'completed_at' => 'datetime',
        ];
    }

    public function getScoreAttribute(): int
    {
        return $this->correct_answers ?? 0;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(VocabularyLesson::class, 'lesson_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(VocabularyPracticeAnswer::class, 'session_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
