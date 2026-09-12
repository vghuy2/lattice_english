<?php

namespace App\Enums;

enum ScoringCriterion: string
{
    case TASK_ACHIEVEMENT_RESPONSE = 'task_achievement_response';
    case COHERENCE_COHESION = 'coherence_cohesion';
    case LEXICAL_RESOURCE = 'lexical_resource';
    case GRAMMATICAL_RANGE_ACCURACY = 'grammatical_range_accuracy';

    public function label(): string
    {
        return match ($this) {
            self::TASK_ACHIEVEMENT_RESPONSE => 'Task Achievement / Task Response (Đáp ứng yêu cầu đề bài)',
            self::COHERENCE_COHESION => 'Coherence & Cohesion (Tính mạch lạc & Liên kết)',
            self::LEXICAL_RESOURCE => 'Lexical Resource (Vốn từ vựng & Độ chuẩn xác)',
            self::GRAMMATICAL_RANGE_ACCURACY => 'Grammatical Range & Accuracy (Độ đa dạng & Chuẩn xác ngữ pháp)',
        };
    }

    public function shortCode(): string
    {
        return match ($this) {
            self::TASK_ACHIEVEMENT_RESPONSE => 'TA/TR',
            self::COHERENCE_COHESION => 'CC',
            self::LEXICAL_RESOURCE => 'LR',
            self::GRAMMATICAL_RANGE_ACCURACY => 'GRA',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TASK_ACHIEVEMENT_RESPONSE => 'Đánh giá khả năng hoàn thành đầy đủ yêu cầu của đề bài, nêu rõ xu hướng/tổng quan (Task 1) hoặc lập trường, luận điểm rõ ràng (Task 2) và đạt dung lượng tối thiểu.',
            self::COHERENCE_COHESION => 'Đánh giá cấu trúc phân chia đoạn văn, sự phát triển ý tưởng logic, việc sử dụng các từ nối (linking words) và đại từ chỉ định mượt mà.',
            self::LEXICAL_RESOURCE => 'Đánh giá độ phong phú của từ vựng học thuật, sử dụng collocations tự nhiên, độ đa dạng và tránh lặp từ quá mức.',
            self::GRAMMATICAL_RANGE_ACCURACY => 'Đánh giá sự đa dạng về cấu trúc câu (câu đơn, câu ghép, câu phức, mệnh đề quan hệ, câu bị động) và mức độ chuẩn xác của ngữ pháp, thì và dấu câu.',
        };
    }
}
