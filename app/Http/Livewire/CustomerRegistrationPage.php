<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerRegistrationPage extends FrontendPage
{
    use TrimEmptyStrings;

    public String $idNumber = '';
    public String $name = '';
    public String $phone = '';
    public String $phoneCountry = '';

    protected $queryString = [
        'idNumber' => ['except' => ''],
    ];

    protected function rules() {
        return [
            'idNumber' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
                'unique:customers,id_number',
            ],
            'name' => 'required',
            'phone' => [
                'required',
                'phone:phoneCountry,mobile',
            ],
            'phoneCountry' => 'required_with:phone',
        ];
    }

    protected function title()
    {
        return __('Customer Registration');
    }

    public function mount()
    {
        $this->phoneCountry = setting()->get('order.default_phone_country', '');
    }

    public function render()
    {
        return parent::view('livewire.customer-registration-page', []);
    }

    public function submit()
    {
        $this->validate();

        $customer = Customer::create([
            'name' => $this->name,
            'id_number' => $this->idNumber,
            'phone' => PhoneNumber::make($this->phone, $this->phoneCountry)->formatE164(),
            'locale' => app()->getLocale(),
            'credit' => setting()->get('customer.starting_credit', config('shop.customer.starting_credit')),
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('shop-front');
    }
}
