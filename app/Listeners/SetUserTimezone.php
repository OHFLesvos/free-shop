<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;

class SetUserTimezone
{
    /**
     * Handle the event.
     *
     * @param Registered $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $this->apply($event->user);
    }

    private function apply(User $user)
    {
        if ($user->timezone === null) {
            $location = geoip()->getLocation();
            if (! $location->default) {
                $user->timezone = $location->timezone ?? null;
                $user->save();
            }
        }
    }
}
