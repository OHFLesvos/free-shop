<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserRolesChanged;
use App\Listeners\EnsureAdminExists;
use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogUserCreated;
use App\Listeners\LogUserDeleted;
use App\Listeners\LogUserLogout;
use App\Listeners\LogUserRolesChanged;
use App\Listeners\SetUserTimezone;
use App\Listeners\UpdateUserLastLogin;
use App\Models\Customer;
use App\Observers\CustomerObserver;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            SetUserTimezone::class,
        ],
        Login::class => [
            EnsureAdminExists::class,
            UpdateUserLastLogin::class,
            LogSuccessfulLogin::class,
        ],
        Failed::class => [
            LogFailedLogin::class,
        ],
        Logout::class => [
            LogUserLogout::class,
        ],
        UserCreated::class => [
            LogUserCreated::class,
        ],
        UserDeleted::class => [
            LogUserDeleted::class,
        ],
        UserRolesChanged::class => [
            LogUserRolesChanged::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        Customer::class => [CustomerObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
