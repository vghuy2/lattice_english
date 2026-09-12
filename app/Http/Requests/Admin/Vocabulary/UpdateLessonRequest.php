<?php

namespace App\Http\Requests\Admin\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $lessonId = $this->route('lesson')?->id ?? $this->input('id');

        return [
            'topic_id' => ['required', 'exists:vocabulary_topics,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('vocabulary_lessons', 'slug')->ignore($lessonId)],
            'description' => ['nullable', 'string'],
            'level' => ['required', Rule::enum(VocabularyLevel::class)],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:180'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'topic_id.required' => 'Vui lòng chọn chủ đề cho bài học.',
            'title.required' => 'Vui lòng nhập tiêu đề bài học.',
            'level.required' => 'Vui lòng chọn cấp độ Band điểm.',
            'estimated_minutes.required' => 'Vui lòng nhập thời gian học dự kiến.',
            'status.required' => 'Vui lòng chọn trạng thái bài học.',
        ];
    }
}
