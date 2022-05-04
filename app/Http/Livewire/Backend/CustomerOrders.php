<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;

class CustomerOrders extends Component
{
    public Customer $customer;

    public function render()
    {
        return view('livewire.backend.customer-orders', [
            'orders' => $this->customer->orders()
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }
}
