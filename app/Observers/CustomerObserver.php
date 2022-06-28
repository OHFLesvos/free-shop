<?php

namespace App\Observers;

use App\Models\Customer;

class CustomerObserver
{
    public function deleting(Customer $customer): void
    {
        $customer->orders()
            ->whereIn('status', ['new', 'ready'])
            ->update(['status' => 'cancelled']);

        $customer->orders()
            ->update(['customer_id' => null]);

        $customer->comments()
            ->delete();
    }
}
