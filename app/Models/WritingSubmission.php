<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WritingSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'writing_prompt_id',
        'essay_content',
        'word_count',
        'time_spent_seconds',
        'status',
        'overall_score',
        'ta_score',
        'cc_score',
        'lr_score',
        'gra_score',
        'scoring_breakdown',
        'feedback_notes',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
            'overall_score' => 'float',
            'ta_score' => 'float',
            'cc_score' => 'float',
            'lr_score' => 'float',
            'gra_score' => 'float',
            'scoring_breakdown' => 'array',
            'word_count' => 'integer',
            'time_spent_seconds' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(WritingPrompt::class, 'writing_prompt_id');
    }

    public function isGraded(): bool
    {
        return $this->status === SubmissionStatus::GRADED;
    }

    public function isDraft(): bool
    {
        return $this->status === SubmissionStatus::DRAFT;
    }

    public function getTimeSpentFormattedAttribute(): string
    {
        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeGraded(Builder $query): Builder
    {
        return $query->where('status', SubmissionStatus::GRADED->value);
    }

    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('status', SubmissionStatus::DRAFT->value);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereIn('status', [
            SubmissionStatus::SUBMITTED->value,
            SubmissionStatus::GRADING->value,
            SubmissionStatus::GRADED->value,
        ]);
    }
}
