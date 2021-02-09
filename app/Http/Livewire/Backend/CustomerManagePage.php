<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerManagePage extends BackendPage
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
        'customer.credit' => [
            'integer',
            'min:0',
        ],
        'customer.remarks' => 'nullable',
        'customer.locale' => 'nullable',
    ];

    public function mount()
    {
        if (! isset($this->customer)) {
            $this->customer = new Customer();
            $this->customer->credit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
        }

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

    protected function title()
    {
        return $this->customer->exists
            ? 'Edit Customer ' . $this->customer->name
            : 'Register Customer';
    }

    public function render()
    {
        return parent::view('livewire.backend.customer-form', [
            'title' => $this->customer->exists ? 'Edit Customer' : 'Register Customer',
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
