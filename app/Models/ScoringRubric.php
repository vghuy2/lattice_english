<?php

namespace App\Models;

use App\Enums\ScoringCriterion;
use App\Enums\WritingTaskType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringRubric extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_type',
        'criterion',
        'band_score',
        'description',
        'key_indicators',
    ];

    protected function casts(): array
    {
        return [
            'task_type' => WritingTaskType::class,
            'criterion' => ScoringCriterion::class,
            'band_score' => 'float',
            'key_indicators' => 'array',
        ];
    }

    public function scopeForTask(Builder $query, WritingTaskType|string $taskType): Builder
    {
        $val = $taskType instanceof WritingTaskType ? $taskType->value : $taskType;
        return $query->where('task_type', $val);
    }

    public function scopeForCriterion(Builder $query, ScoringCriterion|string $criterion): Builder
    {
        $val = $criterion instanceof ScoringCriterion ? $criterion->value : $criterion;
        return $query->where('criterion', $val);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('task_type', 'asc')
            ->orderBy('criterion', 'asc')
            ->orderBy('band_score', 'asc');
    }
}
