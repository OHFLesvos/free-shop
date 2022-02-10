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

    public User $user;

    public array $previousRoles;

    /**
     * Create a new event instance.
     *
     * @param User $user the user whose roles have been changes
     * @param array $previousRoles the names of the user's roles before the change
     */
    public function __construct(User $user, array $previousRoles)
    {
        $this->user = $user;
        $this->previousRoles = $previousRoles;
    }
}
