<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case BANNED = 'banned';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Đang hoạt động',
            self::BANNED => 'Đã bị khóa',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
