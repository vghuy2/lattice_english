<?php

namespace App\Enums;

enum WritingTaskType: string
{
    case TASK_1 = 'task_1';
    case TASK_2 = 'task_2';

    public function label(): string
    {
        return match ($this) {
            self::TASK_1 => 'IELTS Writing Task 1',
            self::TASK_2 => 'IELTS Writing Task 2',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::TASK_1 => 'Task 1',
            self::TASK_2 => 'Task 2',
        };
    }

    public function defaultWordCount(): int
    {
        return match ($this) {
            self::TASK_1 => 150,
            self::TASK_2 => 250,
        };
    }

    public function defaultMinutes(): int
    {
        return match ($this) {
            self::TASK_1 => 20,
            self::TASK_2 => 40,
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::TASK_1 => 'bg-cyan-500/10 text-cyan-700 border-cyan-500/20',
            self::TASK_2 => 'bg-indigo-500/10 text-indigo-700 border-indigo-500/20',
        };
    }
}
