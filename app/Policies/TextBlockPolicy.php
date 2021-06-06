<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TextBlockPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->can('manage text blocks')) {
            return true;
        }
    }

    public function update(User $user)
    {
        if ($user->can('manage text blocks')) {
            return true;
        }
    }
}
