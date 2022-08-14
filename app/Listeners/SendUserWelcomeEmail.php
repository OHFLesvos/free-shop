<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Auth\Events\Registered;

class SendUserWelcomeEmail
{
    public function handle(Registered $event): void
    {
        if ($event->user instanceof User) {
            $event->user->notify(new UserRegistered());
        }
    }
}
