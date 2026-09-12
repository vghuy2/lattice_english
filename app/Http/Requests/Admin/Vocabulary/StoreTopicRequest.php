<?php

namespace App\Http\Requests\Admin\Vocabulary;

use App\Enums\ContentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:vocabulary_topics,slug'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên chủ đề từ vựng.',
            'slug.unique' => 'Đường dẫn tĩnh (slug) này đã tồn tại.',
            'image.image' => 'Tệp ảnh chủ đề không hợp lệ.',
            'image.max' => 'Dung lượng ảnh tối đa 2MB.',
            'status.required' => 'Vui lòng chọn trạng thái chủ đề.',
        ];
    }
}
