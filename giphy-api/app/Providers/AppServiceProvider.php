<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Rating;
use App\Policies\CommentPolicy;
use App\Policies\RatingPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
// use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('files', function ($app) {
            return new \Illuminate\Filesystem\Filesystem();
        });
    }

    public function boot()
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        // Gate::policy(Rating::class, RatingPolicy::class);
        // Gate::policy(Comment::class, CommentPolicy::class);
    }
}