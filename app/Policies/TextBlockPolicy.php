<?php

namespace App\Policies;

use App\Models\TextBlock;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TextBlockPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->can('manage text blocks')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TextBlock  $textBlock
     * @return mixed
     */
    public function update(User $user, TextBlock $textBlock)
    {
        if ($user->can('manage text blocks')) {
            return true;
        }
    }
}
