<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        $this->writeLog($event->user);
    }

    private function writeLog(User $user)
    {
        Log::info('Successful user login.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'start',
            'event.outcome' => 'success',
            'user.name' => $user->name,
            'user.email' => $user->email,
            'user.roles' => $user->getRoleNames()->toArray(),
        ]);
    }
}
