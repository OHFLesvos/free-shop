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
     * @return void
     */
    public function handle(Logout $event)
    {
        if ($event->user instanceof User) {
            $this->writeLog($event->user);
        }
    }

    private function writeLog(User $user): void
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
