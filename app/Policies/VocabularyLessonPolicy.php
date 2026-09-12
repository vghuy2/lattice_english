<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VocabularyLesson;

class VocabularyLessonPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, VocabularyLesson $lesson): bool
    {
        if ($user && $user->isAdmin()) {
            return true;
        }

        return $lesson->isAvailableForStudents();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, VocabularyLesson $lesson): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, VocabularyLesson $lesson): bool
    {
        return $user->isAdmin();
    }
}
