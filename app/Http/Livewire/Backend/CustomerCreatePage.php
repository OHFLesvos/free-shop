<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;

class CustomerCreatePage extends CustomerManagePage
{
    public function mount()
    {
        $this->customer = new Customer();
        parent::mount();
    }
}
