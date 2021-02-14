<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\User;
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
        $this->writeLog($event->user);
    }

    private function writeLog(User $user)
    {
        Log::info('User has been created.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'creation',
            'user.name' => $user->name,
            'user.email' => $user->email,
        ]);
    }
}
