<?php

namespace App\Enums;

enum WordStudyStatus: string
{
    case LEARNING = 'learning';
    case KNOWN = 'known';
    case REVIEW_NEEDED = 'review_needed';

    public function label(): string
    {
        return match ($this) {
            self::LEARNING => 'Chưa nhớ / Đang học',
            self::KNOWN => 'Đã biết / Thuộc từ',
            self::REVIEW_NEEDED => 'Cần ôn tập lại',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LEARNING => 'bg-amber-50 text-amber-700 border-amber-200',
            self::KNOWN => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::REVIEW_NEEDED => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
