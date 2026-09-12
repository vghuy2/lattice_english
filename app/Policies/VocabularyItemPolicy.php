<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VocabularyItem;

class VocabularyItemPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, VocabularyItem $item): bool
    {
        if ($user && $user->isAdmin()) {
            return true;
        }

        return $item->lesson && $item->lesson->isAvailableForStudents();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, VocabularyItem $item): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, VocabularyItem $item): bool
    {
        return $user->isAdmin();
    }
}
