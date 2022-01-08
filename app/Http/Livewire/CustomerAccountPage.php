<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\TrimAndNullEmptyStrings;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerAccountPage extends FrontendPage
{
    use TrimAndNullEmptyStrings;

    public Customer $customer;
    public string $name = '';
    public string $idNumber = '';
    public ?string $phone = null;
    public string $phoneCountry;
    public ?string $email = null;

    public bool $shouldDelete = false;

    protected function rules(): array {
        return [
            'name' => 'required',
            'idNumber' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
                Rule::unique('customers', 'id_number')->ignore($this->customer->id),
            ],
            'phone' => [
                'nullable',
                'required_without:email',
                'phone:phoneCountry,mobile',
            ],
            'phoneCountry' => 'required_with:phone',
            'email' => [
                'nullable',
                'required_without:phone',
                'email',
            ],
        ];
    }

    protected function title(): string
    {
        return __('Customer Account');
    }

    public function mount(): void
    {
        $this->customer = Auth::guard('customer')->user();

        $this->name = $this->customer->name;
        $this->idNumber = $this->customer->id_number;
        try {
            $phone = PhoneNumber::make($this->customer->phone);
            $this->phoneCountry = $phone->getCountry();
            $this->phone = $phone->formatNational();
        } catch (NumberParseException $ignored) {
            $this->phoneCountry = '';
            $this->phone = $this->customer->phone;
        }
        $this->email = $this->customer->email;
    }

    public function render(): View
    {
        return parent::view('livewire.customer-account-page');
    }

    public function submit(): void
    {
        $this->validate();

        $this->customer->name = $this->name;
        $this->customer->id_number = $this->idNumber;
        $this->customer->phone = isset($this->phone)
            ? PhoneNumber::make($this->phone, $this->phoneCountry)->formatE164()
            : null;
        $this->customer->email = $this->email;
        $this->customer->save();

        Log::info('Customer updated profile.', [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => $this->customer->name,
            'customer.id_number' => $this->customer->id_number,
            'customer.phone' => $this->customer->phone,
            'customer.email' => $this->customer->email,
        ]);

        session()->flash('submitMessage', __('Customer profile saved.'));
    }

    public function getCanDeleteProperty(): bool
    {
        return !$this->customer->orders()->exists();
    }

    public function delete()
    {
        if ($this->getCanDeleteProperty()) {
            $this->customer->delete();

            return redirect(route('home'));
        }
    }
}
