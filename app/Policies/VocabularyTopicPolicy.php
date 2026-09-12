<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VocabularyTopic;

class VocabularyTopicPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, VocabularyTopic $topic): bool
    {
        if ($user && $user->isAdmin()) {
            return true;
        }

        return $topic->isPublished();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, VocabularyTopic $topic): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, VocabularyTopic $topic): bool
    {
        return $user->isAdmin();
    }
}
