<?php

namespace App\Http\Requests\Student;

use App\Enums\IeltsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isStudent();
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
            'current_band.required' => 'Vui lòng chọn band điểm hiện tại của bạn.',
            'target_band.required' => 'Vui lòng chọn band điểm mục tiêu.',
            'target_band.gte' => 'Band điểm mục tiêu phải lớn hơn hoặc bằng band điểm hiện tại.',
            'test_type.required' => 'Vui lòng chọn hình thức thi IELTS (Academic hoặc General Training).',
            'target_date.after_or_equal' => 'Ngày thi dự kiến không được ở trong quá khứ.',
            'study_days_per_week.required' => 'Vui lòng chọn số ngày bạn dự định học mỗi tuần.',
            'study_days_per_week.min' => 'Số ngày học tối thiểu là 1 ngày/tuần.',
            'study_days_per_week.max' => 'Số ngày học tối đa là 7 ngày/tuần.',
            'study_goal.max' => 'Mục tiêu học tập không được vượt quá 1000 ký tự.',
        ];
    }
}
