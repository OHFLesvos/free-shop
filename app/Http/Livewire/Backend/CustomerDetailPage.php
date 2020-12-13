<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;

class CustomerDetailPage extends Component
{
    public Customer $customer;

    public function render()
    {
        return view('livewire.backend.customer-detail-page')
            ->layout('layouts.backend', ['title' => 'Customer' . $this->customer->name]);
    }
}
