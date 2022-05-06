<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;

class CustomerLatestOrder extends Component
{
    public Customer $customer;

    public function render()
    {
        return view('livewire.backend.customer-latest-order', [
            'lastOrder' => $this->customer->orders()
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->first(),
            'hasMoreOrders' =>  $this->customer->orders()->count() > 1,
        ]);
    }
}
