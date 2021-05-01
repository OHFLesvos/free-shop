<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use Illuminate\Support\Facades\Log;

class LogUserDeleted
{
    /**
     * Handle the event.
     *
     * @param  UserDeleted  $event
     * @return void
     */
    public function handle(UserDeleted $event)
    {
        Log::info('User has been deleted.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'deletion',
            'user.name' => $event->user->name,
            'user.email' => $event->user->email,
            'user.roles' => $event->user->getRoleNames()->toArray(),
        ]);
    }
}
