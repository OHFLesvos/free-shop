<?php

namespace App\Http\Livewire;

use App\Facades\CurrentCustomer;
use Livewire\Component;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerAccountPage extends Component
{
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

    public function mount()
    {
        $customer = CurrentCustomer::get();
        $this->customer_name = $customer->name;
        $this->customer_id_number = $customer->id_number;
        try {
            $phone = PhoneNumber::make($customer->phone);
            $this->customer_phone_country = $phone->getCountry();
            $this->customer_phone = $phone->formatNational();
        } catch (NumberParseException $ignored) {
            $this->customer_phone_country = '';
            $this->customer_phone = $customer->phone;
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

        $customer = CurrentCustomer::get();
        $customer->name = trim($this->customer_name);
        $customer->id_number = trim($this->customer_id_number);
        $customer->phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();
        $customer->save();

        session()->flash('message', __('Customer profile saved.'));
    }
}
