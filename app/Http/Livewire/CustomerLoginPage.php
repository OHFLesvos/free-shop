<?php

namespace App\Http\Livewire;

use App\Facades\CurrentCustomer;
use App\Models\Customer;
use App\Notifications\OtpRequired;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerLoginPage extends Component
{
    use TrimEmptyStrings;

    private const OTP_LENGTH = 4;

    public string $state = 'enter_id_number';

    public ?Customer $customer = null;

    public string $customer_id_number = '';
    public string $customer_name = '';
    public string $customer_phone = '';
    public string $customer_phone_country;

    public string $otp_value = '';

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
        return view('livewire.customer-login-page', [
                'otp_length' => self::OTP_LENGTH,
            ])
            ->layout(null, ['title' => __('Customer Login')]);
    }

    public function submit()
    {
        if ($this->state == 'enter_id_number') {

            $this->validate([
                'customer_id_number' => [
                    'required',
                    setting()->has('customer.id_number_pattern')
                        ? 'regex:' . setting()->get('customer.id_number_pattern')
                        : null,
                ],
            ]);

            $this->customer = Customer::where('id_number', $this->customer_id_number)->first();

            if ($this->customer == null) {
                $this->changeState('enter_name');
            } else {
                $this->changeState('ask_for_tfa');
            }

        } else if ($this->state == 'enter_name') {

            $this->validate([
                'customer_name' => 'required',
            ]);

            $this->changeState('enter_phone');

        } else if ($this->state == 'enter_phone') {

            $this->validate([
                'customer_phone' => [
                    'required',
                    'phone:customer_phone_country,mobile',
                ],
                'customer_phone_country' => 'required_with:customer_phone',
            ]);

            $this->customer = Customer::make([
                'name' => $this->customer_name,
                'id_number' => $this->customer_id_number,
                'phone' => $this->customerPhoneE164,
                'locale' => app()->getLocale(),
            ]);

            $this->changeState('validate_phone');

        } else if (in_array($this->state, ['ask_for_tfa', 'validate_phone'])) {

            $this->validate([
                'otp_value' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (session()->get('customer-login.otp') != $value) {
                            $fail(__('The code is invalid.'));
                        }
                    }
                ]
            ]);
            session()->forget('customer-login.otp');

            if ($this->customer == null || !$this->customer->exists) {
                $this->customer = Customer::create([
                    'name' => $this->customer_name,
                    'id_number' => $this->customer_id_number,
                    'phone' => $this->customerPhoneE164,
                    'locale' => app()->getLocale(),
                    'credit' => setting()->get('customer.starting_credit', config('shop.customer.starting_credit')),
                ]);
            }

            CurrentCustomer::set($this->customer);
            return redirect()->route('shop-front');

        }
    }

    public function getCustomerPhoneE164Property()
    {
        return filled($this->customer_phone) && filled($this->customer_phone_country)
            ? PhoneNumber::make($this->customer_phone, $this->customer_phone_country)->formatE164()
            : null;
    }

    public function getHeadingProperty()
    {
        if ($this->customer != null && $this->customer->exists) {
            return __('Customer Login');
        }
        if ($this->state != 'enter_id_number') {
            return __('Customer Registration');
        }
        return __('Customer Registration & Login');
    }

    public function changeState($state)
    {
        if ($state == 'enter_id_number') {
            $this->emit('idNumberRequired');
        } else if ($state == 'enter_name') {
            $this->emit('nameRequired');
        } else if ($state == 'enter_phone') {
            $this->emit('phoneRequired');
        } else if (in_array($state, ['ask_for_tfa', 'validate_phone'])) {
            $this->sentOtp();
            $this->emit('otpRequired');
        }
        $this->state = $state;
    }

    public function initialState()
    {
        $this->reset(['customer', 'customer_id_number', 'customer_name', 'customer_phone']);
        $this->changeState('enter_id_number');
        session()->forget('customer-login.otp');
    }

    private function sentOtp()
    {
        $this->otp_value = '';
        $otp = randomNumberPadded(self::OTP_LENGTH);
        session()->put('customer-login.otp', $otp);
        try {
            $this->customer->notify(new OtpRequired($otp));
        } catch (\Exception $ex) {
            Log::warning('[' . get_class($ex) . '] Unable to send OTP \'' . $otp . '\': ' . $ex->getMessage());
        }
    }
}
