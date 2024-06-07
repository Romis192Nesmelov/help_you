<?php

namespace App\Providers;

use App\Events\ChangePasswordEvent;
use App\Listeners\Admin\ChangeUserListener;
use App\Listeners\Admin\NewUserListener;
use App\Listeners\ChangePasswordListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
//            SendEmailVerificationNotification::class,
            NewUserListener::class
        ],

        Verified::class => [
            ChangeUserListener::class
        ],

        ChangePasswordEvent::class => [
            ChangePasswordListener::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
