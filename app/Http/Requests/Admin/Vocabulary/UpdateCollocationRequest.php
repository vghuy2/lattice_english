<?php

namespace App\Http\Requests\Admin\Vocabulary;

use App\Enums\CollocationType;
use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'topic_id' => ['nullable', 'exists:vocabulary_topics,id'],
            'phrase' => ['required', 'string', 'max:255'],
            'meaning' => ['required', 'string', 'max:500'],
            'type' => ['required', Rule::enum(CollocationType::class)],
            'level' => ['required', Rule::enum(VocabularyLevel::class)],
            'example_sentence' => ['nullable', 'string'],
            'example_sentence_vi' => ['nullable', 'string'],
            'writing_notes' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'phrase.required' => 'Vui lòng nhập cụm collocation.',
            'meaning.required' => 'Vui lòng nhập ý nghĩa tiếng Việt.',
            'type.required' => 'Vui lòng chọn phân loại cụm từ (Collocation Type).',
            'level.required' => 'Vui lòng chọn trình độ (Level).',
            'status.required' => 'Vui lòng chọn trạng thái xuất bản.',
        ];
    }
}
