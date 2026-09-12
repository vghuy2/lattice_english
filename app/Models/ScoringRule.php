<?php

namespace App\Models;

use App\Enums\ScoringCriterion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_key',
        'name',
        'description',
        'task_type',
        'criterion',
        'parameters',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'criterion' => ScoringCriterion::class,
            'parameters' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
