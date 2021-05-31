<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerAccountPage extends FrontendPage
{
    public Customer $customer;
    public string $customer_name = '';
    public string $customer_id_number = '';
    public string $customer_phone = '';
    public string $customer_phone_country;

    public bool $shouldDelete = false;

    protected function rules() {
        return [
            'customer_name' => 'required',
            'customer_id_number' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
                'unique:customers,id_number,' . $this->customer->id,
            ],
            'customer_phone' => [
                'required',
                'phone:customer_phone_country,mobile',
            ],
            'customer_phone_country' => 'required_with:customer_phone',
        ];
    }

    protected function title()
    {
        return __('Customer Account');
    }

    public function mount()
    {
        $this->customer = Auth::guard('customer')->user();

        $this->customer_name = $this->customer->name;
        $this->customer_id_number = $this->customer->id_number;
        try {
            $phone = PhoneNumber::make($this->customer->phone);
            $this->customer_phone_country = $phone->getCountry();
            $this->customer_phone = $phone->formatNational();
        } catch (NumberParseException $ignored) {
            $this->customer_phone_country = '';
            $this->customer_phone = $this->customer->phone;
        }
    }

    public function render()
    {
        return parent::view('livewire.customer-account-page');
    }

    public function submit()
    {
        $this->validate();

        $this->customer->name = trim($this->customer_name);
        $this->customer->id_number = trim($this->customer_id_number);
        $this->customer->phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();
        $this->customer->save();

        Log::info('Customer updated profile.', [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => $this->customer->name,
            'customer.email' => $this->customer->id_number,
            'customer.phone' => $this->customer->phone,
        ]);

        session()->flash('submitMessage', __('Customer profile saved.'));
    }

    public function getCanDeleteProperty(): bool
    {
        return !$this->customer->orders()->exists();
    }

    public function delete()
    {
        if ($this->canDelete) {
            $this->customer->delete();

            return redirect(route('home'));
        }
    }
}
