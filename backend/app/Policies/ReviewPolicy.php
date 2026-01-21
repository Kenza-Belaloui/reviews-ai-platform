<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    public function view(User $user, Review $review): bool
    {
        return $user->role === 'admin' || $review->user_id === $user->id;
    }

    public function update(User $user, Review $review): bool
    {
        return $user->role === 'admin' || $review->user_id === $user->id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->role === 'admin' || $review->user_id === $user->id;
    }
}