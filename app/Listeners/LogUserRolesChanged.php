<?php

namespace App\Listeners;

use App\Events\UserRolesChanged;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Log;

class LogUserRolesChanged
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(UserRolesChanged $event)
    {
        $added = $event->user->getRoleNames()
            ->diff($event->previousRoles)
            ->values()
            ->toArray();
        $removed = collect($event->previousRoles)
            ->diff($event->user->getRoleNames())
            ->values()
            ->toArray();

        Log::info('Updated user roles.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'change',
            'user.name' => $event->user->name,
            'user.email' => $event->user->email,
            'user.roles' => $event->user->getRoleNames()->toArray(),
            'user.roles.added' => $added,
            'user.roles.removed' => $removed,
        ]);

        if (in_array(AuthServiceProvider::ADMINISTRATOR_ROLE, $added)) {
            Log::warning('Assigned administrator role to user.', [
                'event.kind' => 'event',
                'event.category' => 'iam',
                'event.type' => 'admin',
                'user.name' => $event->user->name,
                'user.email' => $event->user->email,
            ]);
        }
    }
}
