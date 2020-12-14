<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;

class CustomerDetailPage extends BackendPage
{
    public Customer $customer;

    protected function title() {
        return 'Customer ' . $this->customer->name;
    }

    public function render()
    {
        return parent::view('livewire.backend.customer-detail-page');
    }
}
