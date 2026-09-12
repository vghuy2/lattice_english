<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Quản trị viên (Admin)',
            self::STUDENT => 'Học viên (Student)',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isStudent(): bool
    {
        return $this === self::STUDENT;
    }
}
