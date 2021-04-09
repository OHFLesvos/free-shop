<?php

namespace App\Http\Livewire;

use App\Exceptions\OtpTokenThrottledException;
use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Customer;
use App\Notifications\OtpRequired;
use App\Services\OtpProvider;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twilio\Exceptions\InvalidConfigException;
use Propaganistas\LaravelPhone\PhoneNumber;

class OldCustomerLoginPage extends Component
{
    use TrimEmptyStrings;

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

    public function render(OtpProvider $otp)
    {
        return view('livewire.customer-login-page', [
                'otp_length' => $otp->getTokenLength(),
            ])
            ->layout(null, ['title' => __('Customer Login')]);
    }

    public function submit(OtpProvider $otp)
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

            $this->changeState('validate_phone');

        } else if ($this->state == 'validate_phone') {

            if (!$otp->exists($this->customerPhoneE16)) {
                $this->initialState();
                return;
            }

            $this->validate([
                'otp_value' => [
                    'required',
                    function ($attribute, $value, $fail) use($otp) {
                        if (!$otp->validate($this->customerPhoneE16, $value)) {
                            $fail(__('The code is invalid.'));
                        }
                    }
                ]
            ]);

            $this->customer = Customer::create([
                'name' => $this->customer_name,
                'id_number' => $this->customer_id_number,
                'phone' => $this->customerPhoneE164,
                'locale' => app()->getLocale(),
                'credit' => setting()->get('customer.starting_credit', config('shop.customer.starting_credit')),
            ]);

            CurrentCustomer::set($this->customer);
            return redirect()->route('shop-front'); // TODO redirect to requested page

        } else if ($this->state == 'ask_for_tfa') {

            if (!$otp->exists($this->customer->phone)) {
                $this->initialState();
                return;
            }

            $this->validate([
                'otp_value' => [
                    'required',
                    function ($attribute, $value, $fail) use ($otp) {
                        if (!$otp->validate($this->customer->phone, $value)) {
                            $fail(__('The code is invalid.'));
                        }
                    }
                ]
            ]);

            CurrentCustomer::set($this->customer);
            return redirect()->route('shop-front'); // TODO redirect to requested page

        }
    }

    public function getCustomerPhoneE164Property(): ?string
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
    }

    private function sentOtp()
    {
        $otp = app()->make(OtpProvider::class);

        $this->otp_value = '';

        $customer = $this->customer ?? Customer::make([
            'name' => $this->customer_name,
            'id_number' => $this->customer_id_number,
            'phone' => $this->customerPhoneE164,
            'locale' => app()->getLocale(),
        ]);

        try {
            $code = $otp->create($customer->phone);
            $customer->notify(new OtpRequired($code));
        } catch (OtpTokenThrottledException $ex) {
            session()->flash('otpDelay', __('You can get a new code in :time.', ['time' => $ex->getReadyIn()->diffForHumans()]));
        } catch (CouldNotSendNotification|InvalidConfigException $ex) {
            Log::warning('[' . get_class($ex) . '] Unable to send OTP: ' . $ex->getMessage());
        } catch (PhoneNumberBlockedByAdminException $ex) {
            session()->flash('blocked', __('The phone number :phone has been blocked by an administrator.', ['phone' => $ex->getPhone()]));
        }
    }
}
