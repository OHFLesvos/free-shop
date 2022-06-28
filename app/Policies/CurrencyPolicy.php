<?php

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Currency $currency)
    {
        return true;
    }

    public function create(User $user)
    {
        if ($user->can('manage products')) {
            return true;
        }
    }

    public function update(User $user, Currency $currency)
    {
        if ($user->can('manage products')) {
            return true;
        }
    }

    public function delete(User $user, Currency $currency)
    {
        if ($currency->products()->exists()) {
            return false;
        }

        if ($user->can('manage products')) {
            return true;
        }
    }
}
