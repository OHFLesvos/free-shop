<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        Log::warning('Successful user login.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'start',
            'event.outcome' => 'failure',
            'user.name' => $event->user->name,
            'user.email' => $event->user->email,
        ]);
    }
}
