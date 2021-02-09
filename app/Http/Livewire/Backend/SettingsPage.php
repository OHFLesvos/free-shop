<?php

namespace App\Http\Livewire\Backend;

use Illuminate\Support\Collection;
use Countries;

class SettingsPage extends BackendPage
{
    public Collection $geoblockWhitelist;
    public string $orderDefaultPhoneCountry;
    public string $timezone;
    public string $welcomeText;
    public $customerStartingCredit;
    public bool $shopDisabled;
    public $shopMaxOrdersPerDay;

    public $countries;

    public ?string $selectedCountry = null;

    public bool $welcomeTextPreview = false;

    protected $rules = [
        'shopDisabled' => [
            'boolean',
        ],
        'orderDefaultPhoneCountry' => [
            'nullable',
            'country_code',
        ],
        'shopMaxOrdersPerDay' => [
            'nullable',
            'integer',
            'min:1',
        ],
        'timezone' => [
            'nullable',
            'timezone',
        ],
        'welcomeText' => [
            'nullable',
            'string',
        ],
        'customerStartingCredit' => [
            'nullable',
            'integer',
            'min:0',
        ],
    ];

    public function mount()
    {
        $this->shopDisabled = setting()->has('shop.disabled');
        $this->geoblockWhitelist = collect(setting()->get('geoblock.whitelist', []));
        $this->orderDefaultPhoneCountry = setting()->get('order.default_phone_country', '');
        $this->timezone = setting()->get('timezone', '');
        $this->welcomeText = setting()->get('content.welcome_text', '');
        $this->customerStartingCredit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
        $this->shopMaxOrdersPerDay = setting()->get('shop.max_orders_per_day', '');

        $this->countries = collect(Countries::getList());
    }

    protected $title = 'Settings';

    public function render()
    {
        return parent::view('livewire.backend.settings-page');
    }

    public function addToGeoblockWhitelist()
    {
        if (filled($this->selectedCountry) && ! $this->geoblockWhitelist->contains($this->selectedCountry)) {
            $this->geoblockWhitelist->push($this->selectedCountry);
        }
        $this->selectedCountry = null;
    }

    public function removeFromGeoblockWhitelist($value)
    {
        $this->geoblockWhitelist = $this->geoblockWhitelist->filter(fn ($val) => $value != $val)->values();
    }

    public function submit()
    {
        $this->validate();

        if ($this->shopDisabled) {
            setting()->set('shop.disabled', true);
        } else {
            setting()->forget('shop.disabled');
        }

        if ($this->geoblockWhitelist->isNotEmpty()) {
            setting()->set('geoblock.whitelist', $this->geoblockWhitelist->toArray());
        } else {
            setting()->forget('geoblock.whitelist');
        }

        if (filled($this->orderDefaultPhoneCountry)) {
            setting()->set('order.default_phone_country', $this->orderDefaultPhoneCountry);
        } else {
            setting()->forget('order.default_phone_country');
        }

        if (filled($this->timezone)) {
            setting()->set('timezone', $this->timezone);
        } else {
            setting()->forget('timezone');
        }

        if (filled($this->welcomeText)) {
            setting()->set('content.welcome_text', $this->welcomeText);
        } else {
            setting()->forget('content.welcome_text');
        }

        if (filled($this->customerStartingCredit)) {
            setting()->set('customer.starting_credit', $this->customerStartingCredit);
        } else {
            setting()->forget('customer.starting_credit');
        }

        if (filled($this->shopMaxOrdersPerDay)) {
            setting()->set('shop.max_orders_per_day', $this->shopMaxOrdersPerDay);
        } else {
            setting()->forget('shop.max_orders_per_day');
        }

        session()->flash('submitMessage', 'Settings saved.');
    }
}
