<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->canAny(['view customers', 'manage customers', 'update customers'])) {
            return true;
        }
    }

    public function view(User $user)
    {
        if ($user->canAny(['view customers', 'manage customers', 'update customers'])) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->can('manage customers')) {
            return true;
        }
    }

    public function update(User $user)
    {
        if ($user->canAny(['manage customers', 'update customers'])) {
            return true;
        }
    }

    public function delete(User $user)
    {
        if ($user->can('manage customers')) {
            return true;
        }
    }
}
