<?php

namespace App\Policies;

use App\Models\BlockedPhoneNumber;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlockedPhoneNumberPolicy
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
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlockedPhoneNumber  $blockedPhoneNumber
     * @return mixed
     */
    public function view(User $user, BlockedPhoneNumber $blockedPhoneNumber)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlockedPhoneNumber  $blockedPhoneNumber
     * @return mixed
     */
    public function update(User $user, BlockedPhoneNumber $blockedPhoneNumber)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlockedPhoneNumber  $blockedPhoneNumber
     * @return mixed
     */
    public function delete(User $user, BlockedPhoneNumber $blockedPhoneNumber)
    {
        if ($user->can('manage blocked numbers')) {
            return true;
        }
    }
}
