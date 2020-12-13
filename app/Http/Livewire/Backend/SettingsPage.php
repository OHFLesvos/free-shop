<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;
use Countries;
use Illuminate\Support\Collection;

class SettingsPage extends Component
{
    public Collection $countries;

    public Collection $geoblockWhitelist;
    public string $orderDefaultPhoneCountry;
    public string $timezone;

    public ?string $selectedCountry = null;

    public function mount()
    {
        $this->countries = collect(Countries::getList('en'));
        $this->geoblockWhitelist = collect(setting()->get('geoblock.whitelist', []));
        $this->orderDefaultPhoneCountry = setting()->get('order.default_phone_country', '');
        $this->timezone = setting()->get('timezone', '');
    }

    public function render()
    {
        return view('livewire.backend.settings-page')
            ->layout('layouts.backend', ['title' => 'Settings']);
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

        session()->flash('message', 'Settings saved.');
    }
}
