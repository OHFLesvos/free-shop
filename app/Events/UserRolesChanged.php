<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRolesChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  User  $user the user whose roles have been changes
     * @param  array  $previousRoles the names of the user's roles before the change
     */
    public function __construct(
        public User $user,
        public array $previousRoles
    ) {
    }
}
