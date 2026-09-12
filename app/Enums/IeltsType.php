<?php

namespace App\Enums;

enum IeltsType: string
{
    case ACADEMIC = 'academic';
    case GENERAL_TRAINING = 'general_training';

    public function label(): string
    {
        return match ($this) {
            self::ACADEMIC => 'IELTS Academic',
            self::GENERAL_TRAINING => 'IELTS General Training',
        };
    }
}
