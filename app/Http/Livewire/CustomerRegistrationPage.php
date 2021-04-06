<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Livewire\Component;

class CustomerRegistrationPage extends Component
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

    public function mount()
    {
        $this->phoneCountry = setting()->get('order.default_phone_country', '');
    }

    public function render()
    {
        return view('livewire.customer-registration-page', [])
            ->layout(null, ['title' => __('Customer Registration')]);
    }

    public function submit()
    {
        $this->validate();

        // $customer = Customer::where('id_number', $this->customerIdNumber)->first();
        // if ($customer !== null) {
        //     Auth::guard('customer')->login($customer);
        //     return redirect()->route('shop-front');
        // } else {
        //     session()->flash('error', __('Unknown ID number.'));
        // }
    }
}
