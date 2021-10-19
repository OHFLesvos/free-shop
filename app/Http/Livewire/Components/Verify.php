<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use App\Models\Customer;
use App\Verify\Service;
use Propaganistas\LaravelPhone\PhoneNumber;

class Verify extends Component
{
    public Customer $customer;

    public string $verificationCode = '';

    public bool $requestCode = false;
    public bool $verified = false;

    public function render()
    {
        return view('livewire.components.verify');
    }

    public function getPhoneNumberFormattedProperty()
    {
        if ($this->customer->phone == null) {
            return null;
        }

        try {
            $phoneNumber = PhoneNumber::make($this->customer->phone);
            return anonymize_number($phoneNumber->formatInternational());
        } catch (\Throwable $ignored) {
        }
        return null;
    }

    public function sendCode(Service $verify, $channel = 'sms')
    {
        // TODO check if phone is configured
        // $phone = $this->customer->phone;
        // $verification = $verify->startVerification($phone, $channel);
        // if (!$verification->isValid()) {
        //     session()->flash('error', implode(', ', $verification->getErrors()));
        //     return;
        // }

        $this->requestCode = true;

        $this->emit('codeRequested');
    }

    public function verify(Service $verify)
    {
        $this->validate([
            'verificationCode' => [
                'filled',
                'min:4',
                'max:10',
            ],
        ]);

        // $verification = $verify->checkVerification($this->customer->phone, $this->verificationCode);
        // if (!$verification->isValid()) {
        //     $this->addError('verificationCode', implode(', ', $verification->getErrors()));
        //     return;
        // }

        $this->verified = true;

        $this->emit('verified');
    }

    public function cancelVerify()
    {
        $this->emit('cancelled');
    }
}
