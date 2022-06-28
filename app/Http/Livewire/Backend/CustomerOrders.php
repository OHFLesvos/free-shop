<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerOrders extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

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
