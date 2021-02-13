<?php

namespace App\Listeners;

use App\Models\User;
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
        $this->writeLog($event->user);
    }

    private function writeLog(User $user)
    {
        Log::warning('Successful user login.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'start',
            'event.outcome' => 'failure',
            'user.name' => $user->name,
            'user.email' => $user->email,
        ]);
    }
}
