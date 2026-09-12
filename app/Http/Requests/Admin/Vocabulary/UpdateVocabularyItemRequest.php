<?php

namespace App\Http\Requests\Admin\Vocabulary;

use App\Enums\PartOfSpeech;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVocabularyItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['required', 'exists:vocabulary_lessons,id'],
            'word' => ['required', 'string', 'max:255'],
            'vietnamese_meaning' => ['required', 'string', 'max:500'],
            'part_of_speech' => ['required', Rule::enum(PartOfSpeech::class)],
            'ipa' => ['nullable', 'string', 'max:100'],
            'audio' => ['nullable'],
            'example_sentence' => ['required', 'string'],
            'example_sentence_vi' => ['nullable', 'string'],
            'collocations' => ['nullable'],
            'synonyms' => ['nullable'],
            'antonyms' => ['nullable'],
            'writing_notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'lesson_id.required' => 'Vui lòng chọn bài học cho từ vựng.',
            'word.required' => 'Vui lòng nhập từ hoặc cụm từ (Word / Phrase).',
            'vietnamese_meaning.required' => 'Vui lòng nhập nghĩa Tiếng Việt.',
            'part_of_speech.required' => 'Vui lòng chọn loại từ (Part of Speech).',
            'example_sentence.required' => 'Vui lòng cung cấp ít nhất một câu ví dụ trong ngữ cảnh bài viết IELTS.',
        ];
    }
}
