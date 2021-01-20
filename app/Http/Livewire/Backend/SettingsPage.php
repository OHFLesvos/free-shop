<?php

namespace App\Http\Livewire\Backend;

use Illuminate\Support\Collection;

class SettingsPage extends BackendPage
{
    public Collection $geoblockWhitelist;
    public string $orderDefaultPhoneCountry;
    public string $timezone;
    public string $welcome_text;

    public ?string $selectedCountry = null;

    public function mount()
    {
        $this->geoblockWhitelist = collect(setting()->get('geoblock.whitelist', []));
        $this->orderDefaultPhoneCountry = setting()->get('order.default_phone_country', '');
        $this->timezone = setting()->get('timezone', '');
        $this->welcome_text = setting()->get('welcome-text', '');
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

        if (filled($this->welcome_text)) {
            setting()->set('welcome-text', $this->welcome_text);
        } else {
            setting()->forget('welcome-text');
        }

        session()->flash('message', 'Settings saved.');
    }
}
