<?php

namespace App\Providers;

use App\Util\Carbon\UserTimeZoneMixin;
use Carbon\Carbon;
use Countries;
use Illuminate\Support\Facades\Validator;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::mixin(new UserTimeZoneMixin());

        Validator::extend('country_code', fn ($attribute, $value, $params) => in_array($value, array_keys(Countries::getList())));
    }
}
