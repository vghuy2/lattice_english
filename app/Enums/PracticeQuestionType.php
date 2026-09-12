<?php

namespace App\Enums;

enum PracticeQuestionType: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case MEANING_SELECT = 'meaning_select';
    case FILL_IN_BLANK = 'fill_in_blank';
    case MATCHING = 'matching';
    case CONTEXTUAL = 'contextual';

    public function label(): string
    {
        return match ($this) {
            self::MULTIPLE_CHOICE => 'Trắc nghiệm chọn từ (Multiple Choice)',
            self::MEANING_SELECT => 'Chọn nghĩa đúng (Meaning Selection)',
            self::FILL_IN_BLANK => 'Điền từ vào câu (Fill in the blank)',
            self::MATCHING => 'Nối từ với nghĩa (Matching)',
            self::CONTEXTUAL => 'Chọn từ theo ngữ cảnh bài thi (Contextual Usage)',
        };
    }

    public function short(): string
    {
        return match ($this) {
            self::MULTIPLE_CHOICE => 'Trắc nghiệm',
            self::MEANING_SELECT => 'Chọn nghĩa',
            self::FILL_IN_BLANK => 'Điền từ',
            self::MATCHING => 'Nối từ',
            self::CONTEXTUAL => 'Ngữ cảnh',
        };
    }
}
