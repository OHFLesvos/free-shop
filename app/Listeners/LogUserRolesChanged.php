<?php

namespace App\Listeners;

use App\Events\UserRolesChanged;
use Illuminate\Support\Facades\Log;

class LogUserRolesChanged
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserRolesChanged $event)
    {
        Log::info('Updated user roles.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'change',
            'user.name' => $event->user->name,
            'user.email' => $event->user->email,
            'user.roles' => $event->user->getRoleNames()->toArray(),
            'user.roles.added' => array_diff($event->user->getRoleNames()->toArray(), $event->previousRoles),
            'user.roles.removed' => array_diff($event->previousRoles, $event->user->getRoleNames()->toArray()),
        ]);
    }
}
