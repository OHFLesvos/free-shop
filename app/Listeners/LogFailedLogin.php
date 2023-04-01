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
     * @return void
     */
    public function handle(Failed $event)
    {
        if ($event->user instanceof User) {
            $this->writeLog($event->user);
        }
    }

    private function writeLog(User $user): void
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
