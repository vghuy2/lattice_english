<?php

namespace App\Models;

use App\Enums\LearningStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'student_lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'completed_items_count',
        'total_items_count',
        'last_studied_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => LearningStatus::class,
            'completed_items_count' => 'integer',
            'total_items_count' => 'integer',
            'last_studied_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(VocabularyLesson::class, 'lesson_id');
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_items_count <= 0) {
            return 0;
        }

        return (int) round(($this->completed_items_count / $this->total_items_count) * 100);
    }

    public function isCompleted(): bool
    {
        return $this->status === LearningStatus::COMPLETED;
    }
}
