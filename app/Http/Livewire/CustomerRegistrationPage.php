<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerRegistrationPage extends FrontendPage
{
    use TrimEmptyStrings;

    public string $idNumber = '';

    public string $name = '';

    public string $phone = '';

    public string $phoneCountry = '';

    public string $email = '';

    /**
     * @var array
     */
    protected $queryString = [
        'idNumber' => ['except' => ''],
    ];

    protected function rules(): array
    {
        return [
            'idNumber' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:'.setting()->get('customer.id_number_pattern')
                    : null,
                'unique:customers,id_number',
            ],
            'name' => 'required',
            'phone' => [
                'required_without:email',
                'phone:phoneCountry,mobile',
            ],
            'phoneCountry' => 'required_with:phone',
            'email' => [
                'required_without:phone',
                'email',
            ],
        ];
    }

    protected function title(): string
    {
        return __('Customer Registration');
    }

    public function mount(): void
    {
        $this->phoneCountry = setting()->get('order.default_phone_country', '');
    }

    public function render(): View
    {
        return parent::view('livewire.customer-registration-page', []);
    }

    public function submit()
    {
        $this->validate();

        /** @var Customer $customer */
        $customer = Customer::create([
            'name' => $this->name,
            'id_number' => $this->idNumber,
            'phone' => filled($this->phone) ? PhoneNumber::make($this->phone, $this->phoneCountry)->formatE164() : null,
            'email' => filled($this->email) ? $this->email : null,
            'locale' => app()->getLocale(),
            'topped_up_at' => now(),
        ]);

        $customer->initializeBalances();

        Auth::guard('customer')->login($customer);

        return redirect()->route('shop-front');
    }
}
