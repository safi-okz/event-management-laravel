<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('update-event', function($user, Event $event){
            return $user->id === $event->user_id;
        });

        Gate::define('delete-event', function($user, Event $event, Attendee $attendee){
            return $user->id === $event->user_id || $user->id === $attendee->user_id;
        });
    }
}
