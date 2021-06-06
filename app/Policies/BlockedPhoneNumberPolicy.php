<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlockedPhoneNumberPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    public function view(User $user)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    public function update(User $user)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    public function delete(User $user)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }
}
