<?php

namespace App\Providers;

use App\Support\SMS\SmsSender;
use App\Support\SMS\TwilioSmsSender;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (config('sms.service') == 'twilio') {
            $this->app->bind(SmsSender::class, TwilioSmsSender::class);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
