<?php

namespace App\Http\Requests\Profile;

use App\Enums\IeltsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoalsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_band' => ['required', 'numeric', 'min:3.0', 'max:9.0'],
            'target_band' => ['required', 'numeric', 'min:3.5', 'max:9.0', 'gte:current_band'],
            'test_type' => ['required', Rule::enum(IeltsType::class)],
            'target_date' => ['nullable', 'date', 'after_or_equal:today'],
            'study_days_per_week' => ['required', 'integer', 'min:1', 'max:7'],
            'study_goal' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_band.required' => 'Vui lòng chọn band hiện tại.',
            'target_band.required' => 'Vui lòng chọn band mục tiêu.',
            'target_band.gte' => 'Band mục tiêu phải lớn hơn hoặc bằng band hiện tại.',
            'test_type.required' => 'Vui lòng chọn hình thức thi IELTS.',
            'target_date.after_or_equal' => 'Ngày thi dự kiến không được trong quá khứ.',
            'study_days_per_week.required' => 'Vui lòng chọn số ngày học mỗi tuần.',
            'study_goal.max' => 'Mục tiêu học tập không quá 1000 ký tự.',
        ];
    }
}
