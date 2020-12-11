<?php

namespace App\Providers;

use App\Events\OrderSubmitted;
use App\Listeners\SendOrderNotificationToCustomer;
use App\Listeners\SendOrderNotificationToStaff;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderSubmitted::class => [
            SendOrderNotificationToStaff::class,
            SendOrderNotificationToCustomer::class,
        ],
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
}
