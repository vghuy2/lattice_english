<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SampleEssay extends Model
{
    use HasFactory;

    protected $fillable = [
        'writing_prompt_id',
        'title',
        'band_score',
        'author_type',
        'essay_text',
        'word_count',
        'analysis_notes',
        'highlighted_vocabulary',
        'highlighted_structures',
        'status',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'band_score' => 'float',
            'word_count' => 'integer',
            'highlighted_vocabulary' => 'array',
            'highlighted_structures' => 'array',
            'order_index' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (SampleEssay $essay) {
            if (empty($essay->word_count) && !empty($essay->essay_text)) {
                $essay->word_count = count(preg_split('/\s+/', trim($essay->essay_text)));
            }
        });
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(WritingPrompt::class, 'writing_prompt_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('band_score', 'asc')->orderBy('order_index', 'asc');
    }
}
