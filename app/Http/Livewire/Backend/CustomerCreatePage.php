<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;

class CustomerCreatePage extends Component
{
    public Customer $customer;

    public bool $shouldDelete = false;

    protected $rules = [
        'customer.name' => 'required',
        'customer.id_number' => 'required',
        'customer.phone' => [
            'required',
            'phone:AUTO',
        ],
        'customer.remarks' => 'nullable',
        'customer.locale' => 'nullable',
    ];

    public function mount()
    {
        $this->customer = new Customer();
    }

    public function render()
    {
        return view('livewire.backend.customer-form')
            ->layout('layouts.backend', ['title' => 'Register Customer']);
    }

    public function submit()
    {
        $this->validate();

        $this->customer->save();

        return redirect()->route('backend.customers');
    }
}
