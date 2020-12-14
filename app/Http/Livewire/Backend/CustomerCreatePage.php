<?php

namespace App\Http\Livewire\Backend;

use Countries;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Livewire\Component;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerCreatePage extends Component
{
    public Customer $customer;

    public bool $shouldDelete = false;

    public Collection $countries;
    public string $customer_phone;
    public string $customer_phone_country;

    protected $rules = [
        'customer.name' => 'required',
        'customer.id_number' => 'required',
        'customer_phone' => [
            'required',
            'phone:customer_phone_country,mobile',
        ],
        'customer.remarks' => 'nullable',
        'customer.locale' => 'nullable',
    ];

    public function mount()
    {
        $this->customer = new Customer();
        $this->countries = collect(Countries::getList(app()->getLocale()));
        $this->customer_phone_country = setting()->get('order.default_phone_country', '');
        $this->customer_phone = '';
    }

    public function render()
    {
        return view('livewire.backend.customer-form')
            ->layout('layouts.backend', ['title' => 'Register Customer']);
    }

    public function submit()
    {
        $this->validate();

        $this->customer->phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();

        $this->customer->save();

        session()->flash('message', 'Customer registered.');

        return redirect()->route('backend.customers');
    }
}
