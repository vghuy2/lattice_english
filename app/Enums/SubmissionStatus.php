<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case GRADING = 'grading';
    case GRADED = 'graded';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Bản nháp',
            self::SUBMITTED => 'Đã nộp bài',
            self::GRADING => 'Đang chấm điểm',
            self::GRADED => 'Đã có kết quả',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-slate-700 border-slate-200',
            self::SUBMITTED => 'bg-amber-50 text-amber-800 border-amber-200',
            self::GRADING => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            self::GRADED => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        };
    }
}
