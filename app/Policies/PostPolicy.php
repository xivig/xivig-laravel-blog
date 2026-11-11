<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy {
    public function viewAny(User $user): bool {
        return $user->hasPermission('view_posts') || $user->isAdmin();
    }

    public function view(User $user, Post $post): bool {
        return true;
    }

    public function create(User $user): bool {
        return $user->hasPermission('create_posts') || $user->isAdmin();
    }

    public function update(User $user, Post $post): bool {
        if ($user->isAdmin() || $user->hasRole('editor')) {
            return true;
        }
        return $user->hasPermission('edit_posts') && $user->author?->id === $post->author_id;
    }

    public function delete(User $user, Post $post): bool {
        return $user->hasPermission('delete_posts') || $user->isAdmin();
    }
}
