<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Rating;
use App\Policies\CommentPolicy;
use App\Policies\RatingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Rating::class => RatingPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}