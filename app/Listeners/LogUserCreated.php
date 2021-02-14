<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;

class LogUserCreated
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        Log::info('User has been created.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'creation',
            'user.name' => $event->user->name,
            'user.email' => $event->user->email,
        ]);
    }
}
