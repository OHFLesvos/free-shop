<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Customer;
use App\Verify\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerLoginPage extends FrontendPage
{
    use TrimEmptyStrings;

    public string $idNumber = '';

    public bool $showVerify = false;

    public string $verificationCode = '';

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

    public function submit(Service $verify)
    {
        $this->validate();

        $customer = Customer::where('id_number', $this->idNumber)->first();
        if ($customer === null) {
            return redirect()->route('customer.registration', [
                'idNumber' => $this->idNumber,
            ]);
        }

        if ($customer->is_disabled) {
            $message = __('Your account has been disabled.');
            if (filled($customer->disabled_reason)) {
                $message .= ' ' . $customer->disabled_reason;
            }
            session()->flash('error', $message);
            return;
        }

        // TODO check if phone is configured
        $channel = 'sms';
        $verification = $verify->startVerification($customer->phone, $channel);
        if (!$verification->isValid()) {
            session()->flash('error', implode(', ', $verification->getErrors()));
            return;
        }

        $this->showVerify = true;
    }

    public function verify(Service $verify)
    {
        $this->validate([
            'verificationCode' => [
                'filled',
            ],
        ]);

        $customer = Customer::where('id_number', $this->idNumber)->first();

        $verification = $verify->checkVerification($customer->phone, $this->verificationCode);
        if (!$verification->isValid()) {
            $this->addError('verificationCode', implode(', ', $verification->getErrors()));
            return;
        }

        Auth::guard('customer')->login($customer);

        return redirect()->route('shop-front');
    }

    public function cancelVerify()
    {
        $this->showVerify = false;

        $this->reset();
    }
}
