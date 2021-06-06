<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->canAny(['manage products', 'update products'])) {
            return true;
        }
    }

    public function view(User $user)
    {
        if ($user->canAny(['manage products', 'update products'])) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->can('manage products')) {
            return true;
        }
    }

    public function update(User $user)
    {
        if ($user->canAny(['manage products', 'update products'])) {
            return true;
        }
    }

    public function delete(User $user, Product $product)
    {
        if ($product->orders()->exists()) {
            return false;
        }

        if ($user->can('manage products')) {
            return true;
        }
    }
}
