<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Services\CurrentCustomer;
use Livewire\Component;

class OrderLookupPage extends Component
{
    public Customer $customer;

    public function mount(CurrentCustomer $currentCustomer)
    {
        $this->customer = $currentCustomer->get();
    }

    public function render()
    {
        $orders = $this->customer->orders()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.order-lookup-page', [
                'orders' => $orders,
            ])
            ->layout(null, ['title' => __('Find your order')]);
    }
}
