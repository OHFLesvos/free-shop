<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerLoginPage extends Component
{
    use TrimEmptyStrings;

    public String $idNumber = '';

    protected function rules() {
        return [
            'idNumber' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.customer-login-page', [])
            ->layout(null, ['title' => __('Customer Registration & Login')]);
    }

    public function submit()
    {
        $this->validate();

        $customer = Customer::where('id_number', $this->idNumber)->first();
        if ($customer !== null) {
            if ($customer->is_disabled) {
                $message = __('Your account has been disabled.');
                if (filled($customer->disabled_reason)) {
                    $message .= ' ' . $customer->disabled_reason;
                }
                session()->flash('error', $message);
                return;
            }
            Auth::guard('customer')->login($customer);
            return redirect()->route('shop-front');
        } else {
            return redirect()->route('customer.registration', [
                'idNumber' => $this->idNumber,
            ]);
        }
    }
}
