<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
                'unique:customers,id_number',
            ],
            'name' => 'required',
            'phone' => [
                'phone:phoneCountry,mobile',
            ],
            'phoneCountry' => 'required_with:phone',
            'email' => [
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

        $customer = Customer::create([
            'name' => $this->name,
            'id_number' => $this->idNumber,
            'phone' => filled($this->phone) ? phone($this->phone, $this->phoneCountry)->formatE164() : null,
            'email' => filled($this->email) ? $this->email : null,
            'locale' => app()->getLocale(),
            'credit' => setting()->get('customer.starting_credit', config('shop.customer.starting_credit')),
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('shop-front');
    }
}
