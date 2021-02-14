<?php

namespace App\Listeners;

use App\Events\UserDeleted;
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
    public function handle(UserDeleted $event)
    {
        $this->writeLog($event->user);
    }

    private function writeLog(User $user)
    {
        Log::info('User has been deleted.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'deletion',
            'user.name' => $user->name,
            'user.email' => $user->email,
            'user.roles' => $user->getRoleNames()->toArray(),
        ]);
    }
}
