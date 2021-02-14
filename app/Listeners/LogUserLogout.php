<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogUserLogout
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $this->writeLog($event->user);
    }

    private function writeLog(User $user)
    {
        Log::info('User logged out.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'end',
            'user.name' => $user->name,
            'user.email' => $user->email,
        ]);
    }
}
