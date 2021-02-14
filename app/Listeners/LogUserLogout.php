<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogUserLogout
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        Log::info('User logged out.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'end',
            'user.name' => $event->user->name,
            'user.email' => $event->user->email,
        ]);
    }
}
