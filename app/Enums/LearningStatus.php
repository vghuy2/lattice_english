<?php

namespace App\Enums;

enum LearningStatus: string
{
    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::NOT_STARTED => 'Chưa học',
            self::IN_PROGRESS => 'Đang học',
            self::COMPLETED => 'Đã hoàn thành',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NOT_STARTED => 'bg-slate-100 text-slate-600 border-slate-200',
            self::IN_PROGRESS => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::COMPLETED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
