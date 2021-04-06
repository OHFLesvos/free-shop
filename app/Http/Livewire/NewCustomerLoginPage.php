<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NewCustomerLoginPage extends Component
{
    use TrimEmptyStrings;

    public String $customerIdNumber = '';

    protected function rules() {
        return [
            'customerIdNumber' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.new-customer-login-page', [])
            ->layout(null, ['title' => __('Customer Login')]);
    }

    public function submit()
    {
        $this->validate();

        $customer = Customer::where('id_number', $this->customerIdNumber)->first();
        if ($customer !== null) {
            Auth::guard('customer')->login($customer);
            return redirect()->route('shop-front');
        } else {
            session()->flash('error', __('Unknown ID number.'));
        }
    }
}
