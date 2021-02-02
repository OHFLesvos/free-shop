<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Services\CurrentCustomer;
use Livewire\Component;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerAccountPage extends Component
{
    public Customer $customer;
    public string $customer_name = '';
    public string $customer_id_number = '';
    public string $customer_phone = '';
    public string $customer_phone_country;

    protected $rules = [
        'customer_name' => 'required',
        'customer_id_number' => 'required',
        'customer_phone' => [
            'required',
            'phone:customer_phone_country,mobile',
        ],
        'customer_phone_country' => 'required_with:customer_phone',
    ];

    public function mount(CurrentCustomer $currentCustomer)
    {
        $this->customer = $currentCustomer->get();

        $this->customer_name = $this->customer->name;
        $this->customer_id_number = $this->customer->id_number;
        try {
            $phone = PhoneNumber::make($this->customer->phone);
            $this->customer_phone_country = $phone->getCountry();
            $this->customer_phone = $phone->formatNational();
        } catch (NumberParseException $ignored) {
            $this->customer_phone_country = '';
            $this->customer_phone = $this->customer->phone;
        }
    }

    public function render()
    {
        return view('livewire.customer-account-page')
            ->layout(null, ['title' => __('Customer Account')]);
    }

    public function submit()
    {
        $this->validate();

        $this->customer->name = trim($this->customer_name);
        $this->customer->id_number = trim($this->customer_id_number);
        $this->customer->phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();
        $this->customer->save();

        session()->flash('message', __('Customer profile saved.'));
    }
}