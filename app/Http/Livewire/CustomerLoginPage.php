<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerLoginPage extends FrontendPage
{
    use TrimEmptyStrings;

    public string $idNumber = '';

    protected function rules(): array
    {
        return [
            'idNumber' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
            ],
        ];
    }

    protected function title(): string
    {
        return __('Customer Registration & Login');
    }

    public function render(): View
    {
        return parent::view('livewire.customer-login-page', []);
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
        }

        return redirect()->route('customer.registration', [
            'idNumber' => $this->idNumber,
        ]);
    }
}
