<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->canAny(['view orders', 'update orders'])) {
            return true;
        }
    }

    public function view(User $user)
    {
        if ($user->canAny(['view orders', 'update orders'])) {
            return true;
        }
    }

    public function update(User $user, Order $order)
    {
        if (!$order->isOpen) {
            return false;
        }

        if ($user->can('update orders')) {
            return true;
        }
    }
}
