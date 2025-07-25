<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatingPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Rating $rating)
    {
        return $user->id === $rating->user_id;
    }

    public function update(User $user, Rating $rating)
    {
        return $user->id === $rating->user_id;
    }

    public function delete(User $user, Rating $rating)
    {
        return $user->id === $rating->user_id;
    }
}