<?php

namespace App\Observers;

use App\Models\Currency;
use App\Models\Customer;

class CustomerObserver
{
    public function creating(Customer $customer): void
    {
        if ($customer->topped_up_at == null) {
            $customer->topped_up_at = now();
        }
    }

    public function created(Customer $customer): void
    {
        $ids = Currency::all()
            ->mapWithKeys(fn (Currency $currency) => [$currency->id => [
                'value' => $currency->initial_value,
            ]]);

        $customer->currencies()->sync($ids);
    }

    public function deleting(Customer $customer): void
    {
        $customer->orders()
            ->whereIn('status', ['new', 'ready'])
            ->update(['status' => 'cancelled']);

        // $customer->orders()
        //     ->update(['customer_id' => null]);

        $customer->comments()
            ->delete();
    }
}
