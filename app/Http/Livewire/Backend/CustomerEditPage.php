<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;

class CustomerEditPage extends Component
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

    public function render()
    {
        return view('livewire.backend.customer-form')
            ->layout('layouts.backend', ['title' => 'Register Customer' . $this->customer->name]);
    }

    public function submit()
    {
        $this->validate();

        $this->customer->save();

        return redirect()->route('backend.customers');
    }

    public function delete()
    {
        $this->customer->delete();

        return redirect()->route('backend.customers');
    }
}
