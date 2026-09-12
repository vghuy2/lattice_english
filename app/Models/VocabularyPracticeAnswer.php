<?php

namespace App\Models;

use App\Enums\PracticeQuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyPracticeAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'vocabulary_item_id',
        'question_type',
        'question_data',
        'user_answer',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'question_type' => PracticeQuestionType::class,
            'question_data' => 'array',
            'is_correct' => 'boolean',
        ];
    }

    public function getCorrectAnswerAttribute(): ?string
    {
        return $this->question_data['correct_answer'] ?? null;
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(VocabularyPracticeSession::class, 'session_id');
    }

    public function vocabularyItem(): BelongsTo
    {
        return $this->belongsTo(VocabularyItem::class, 'vocabulary_item_id');
    }
}
