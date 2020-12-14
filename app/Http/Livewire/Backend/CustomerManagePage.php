<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerManagePage extends Component
{
    public Customer $customer;

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
        $this->customer_phone_country = setting()->get('order.default_phone_country', '');
        $this->customer_phone = '';
        if ($this->customer->phone != null) {
            try {
                $phone = PhoneNumber::make($this->customer->phone);
                $this->customer_phone_country = $phone->getCountry();
                $this->customer_phone = $phone->formatNational();
            } catch (NumberParseException $ignored) {
                $this->customer_phone_country = '';
                $this->customer_phone = $this->customer->phone;
            }
        }
    }

    public function render()
    {
        return view('livewire.backend.customer-form')
            ->layout('layouts.backend', [
                'title' => $this->customer->exists
                    ? 'Edit Customer ' . $this->customer->name
                    : 'Register Customer',
            ]);
    }

    public function submit()
    {
        $this->validate();

        $this->customer->phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();

        $this->customer->save();

        session()->flash('message', $this->customer->wasRecentlyCreated
            ? 'Customer registered.'
            : 'Customer updated.');

        return redirect()->route('backend.customers');
    }

    public function delete()
    {
        $this->customer->delete();

        session()->flash('message', 'Customer deleted.');

        return redirect()->route('backend.customers');
    }
}
