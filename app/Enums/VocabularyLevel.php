<?php

namespace App\Enums;

enum VocabularyLevel: string
{
    case BAND_4_5_5_0 = 'band_4_5_5_0';
    case BAND_5_5_6_0 = 'band_5_5_6_0';
    case BAND_6_5_PLUS = 'band_6_5_plus';

    public function label(): string
    {
        return match ($this) {
            self::BAND_4_5_5_0 => 'Band 4.5 – 5.0 (Cơ bản & Nền tảng)',
            self::BAND_5_5_6_0 => 'Band 5.5 – 6.0 (Trung cấp & Mở rộng)',
            self::BAND_6_5_PLUS => 'Band 6.5+ (Nâng cao & Bứt phá)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::BAND_4_5_5_0 => 'Band 4.5 – 5.0',
            self::BAND_5_5_6_0 => 'Band 5.5 – 6.0',
            self::BAND_6_5_PLUS => 'Band 6.5+',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BAND_4_5_5_0 => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
            self::BAND_5_5_6_0 => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
            self::BAND_6_5_PLUS => 'bg-violet-500/10 text-violet-600 dark:text-violet-400 border-violet-500/20',
        };
    }
}
