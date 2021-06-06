<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->can('manage users')) {
            return true;
        }
    }

    public function view(User $user)
    {
        if ($user->can('manage users')) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->can('manage users')) {
            return true;
        }
    }

    public function update(User $user)
    {
        if ($user->can('manage users')) {
            return true;
        }
    }

    public function delete(User $user, User $model)
    {
        if ($user->id == $model->id) {
            return false;
        }

        if ($user->can('manage users')) {
            return true;
        }
    }
}
