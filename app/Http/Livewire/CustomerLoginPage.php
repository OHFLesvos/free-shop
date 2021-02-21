<?php

namespace App\Http\Livewire;

use App\Facades\CurrentCustomer;
use App\Models\Customer;
use Livewire\Component;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerLoginPage extends Component
{
    public string $customer_name = '';
    public string $customer_id_number = '';
    public string $customer_phone = '';
    public string $customer_phone_country;
    public bool $request_name = false;

    protected function rules() {
        return [
            'customer_name' => 'nullable',
            'customer_id_number' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
            ],
            'customer_phone' => [
                'required',
                'phone:customer_phone_country,mobile',
            ],
            'customer_phone_country' => 'required_with:customer_phone',
        ];
    }

    protected $validationAttributes = [
        'customer_name' => 'name',
        'customer_id_number' => 'ID number',
        'customer_phone' => 'phone number',
    ];

    public function mount()
    {
        $this->customer_phone_country = setting()->get('order.default_phone_country', '');
    }

    public function render()
    {
        return view('livewire.customer-login-page')
            ->layout(null, ['title' => __('Customer Login')]);
    }

    public function submit()
    {
        $this->validate();

        $id_number = trim($this->customer_id_number);
        $phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();

        $customer = Customer::firstOrCreate([
            'id_number' => $id_number,
            'phone' => $phone,
        ],[
            'name' => '',
            'id_number' => $id_number,
            'phone' => $phone,
            'locale' => app()->getLocale(),
            'credit' => setting()->get('customer.starting_credit', config('shop.customer.starting_credit')),
        ]);

        $customer->locale = app()->getLocale();

        $name = trim($this->customer_name);
        if (blank($customer->name) && blank($name)) {
            $this->request_name = true;
            $this->emit('nameRequired');
            return;
        }
        if (filled($name)) {
            $customer->name = $name;
        }
        $customer->save();
        $this->request_name = false;

        CurrentCustomer::set($customer);

        return redirect()->route('shop-front');
    }
}
