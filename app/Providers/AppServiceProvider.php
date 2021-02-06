<?php

namespace App\Providers;

use App\Services\CurrentCustomer;
use App\Util\Carbon\UserTimeZoneMixin;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrentCustomer::class, function ($app) {
            return new CurrentCustomer();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::mixin(new UserTimeZoneMixin());
    }
}
