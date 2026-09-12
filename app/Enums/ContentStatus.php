<?php

namespace App\Enums;

enum ContentStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Bản nháp',
            self::PUBLISHED => 'Đã xuất bản',
            self::ARCHIVED => 'Lưu trữ',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }
}
