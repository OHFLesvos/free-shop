<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Torann\GeoIP\Facades\GeoIP;

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
        if ($event->user instanceof User) {
            $this->setTimezoneIfEmpty($event->user);
        }
    }

    private function setTimezoneIfEmpty(User $user): void
    {
        if ($user->timezone === null) {
            $location = GeoIP::getLocation();
            if (!$location->default) {
                $user->timezone = $location->timezone ?? null;
                $user->save();
            }
        }
    }
}
