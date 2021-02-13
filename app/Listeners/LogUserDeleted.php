<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class LogUserDeleted
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->writeToLog($event->user);
    }

    private function writeToLog(User $user)
    {
        Log::info('User has been deleted.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'deletion',
            'user.name' => $user->name,
            'user.email' => $user->email,
            'user.roles' => $user->getRoleNames(),
            'url.original' => optional(request())->url(),
            'url.domain' => optional(request())->getHost(),
            'client.session.id' => optional(session())->getId(),
            'service.name' => config('app.name'),
        ]);
    }
}
