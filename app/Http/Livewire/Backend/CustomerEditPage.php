<?php

namespace App\Http\Livewire\Backend;

use Countries;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Livewire\Component;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerEditPage extends Component
{
    public Customer $customer;

    public Collection $countries;
    public string $customer_phone;
    public string $customer_phone_country;

    public bool $shouldDelete = false;

    protected $rules = [
        'customer.name' => 'required',
        'customer.id_number' => 'required',
        'customer_phone' => [
            'required',
            'phone:customer_phone_country,mobile',
        ],
        'customer_phone_country' => 'required_with:customer_phone',
        'customer.remarks' => 'nullable',
        'customer.locale' => 'nullable',
    ];

    public function mount()
    {
        $this->countries = collect(Countries::getList(app()->getLocale()));
        $this->customer_phone_country = setting()->get('order.default_phone_country', '');
        $this->customer_phone = $this->customer->phone != null
            ? PhoneNumber::make($this->customer->phone)->formatNational()
            : '';
    }

    public function render()
    {
        return view('livewire.backend.customer-form')
            ->layout('layouts.backend', ['title' => 'Register Customer' . $this->customer->name]);
    }

    public function submit()
    {
        $this->validate();

        $this->customer->phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();

        $this->customer->save();

        session()->flash('message', 'Customer updated.');

        return redirect()->route('backend.customers');
    }

    public function delete()
    {
        $this->customer->delete();

        session()->flash('message', 'Customer deleted.');

        return redirect()->route('backend.customers');
    }
}
