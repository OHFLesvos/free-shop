<?php

namespace App\Http\Livewire\Backend;

use Illuminate\Support\Collection;
use Countries;
use Gumlet\ImageResize;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class SettingsPage extends BackendPage
{
    use WithFileUploads;

    public Collection $geoblockWhitelist;
    public string $orderDefaultPhoneCountry;
    public string $timezone;
    public $welcomeText;
    public $customerStartingCredit;
    public bool $shopDisabled;
    public $shopMaxOrdersPerDay;
    public $customerIdNumberPattern;
    public $customerIdNumberExample;

    public $countries;

    public ?string $selectedCountry = null;

    public bool $welcomeTextPreview = false;

    public $brandLogo;
    public $brandLogoUpload;
    public bool $brandLogoRemove = false;

    public $contentLocale;

    protected function rules() {
        return [
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
                'array',
            ],
            'welcomeText.*' => [
                'string',
            ],
            'customerStartingCredit' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'customerIdNumberPattern' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ((@preg_match($value, null) === false)) {
                        $fail('The pattern is invalid.');
                    }
                }
            ],
            'customerIdNumberExample' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (filled($this->customerIdNumberPattern)) {
                        if (!preg_match($this->customerIdNumberPattern, $value)) {
                            $fail('The example is invalid.');
                        }
                    }
                }
            ]
        ];
    }

    public function mount()
    {
        $this->shopDisabled = setting()->has('shop.disabled');
        $this->geoblockWhitelist = collect(setting()->get('geoblock.whitelist', []));
        $this->orderDefaultPhoneCountry = setting()->get('order.default_phone_country', '');
        $this->timezone = setting()->get('timezone', '');
        $this->welcomeText = setting()->get('content.welcome_text', []);
        $this->customerStartingCredit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
        $this->shopMaxOrdersPerDay = setting()->get('shop.max_orders_per_day', '');
        $this->brandLogo = setting()->get('brand.logo');
        $this->customerIdNumberPattern = setting()->get('customer.id_number_pattern', '');
        $this->customerIdNumberExample = setting()->get('customer.id_number_example', '');

        $this->countries = collect(Countries::getList());

        $this->contentLocale = config('app.fallback_locale');
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

        $welcomeText = collect($this->welcomeText)
            ->filter(fn ($t) => filled($t))
            ->toArray();
        if (count($welcomeText) > 0) {
            setting()->set('content.welcome_text', $welcomeText);
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

        if (setting()->has('brand.logo') && ($this->brandLogoRemove) || isset($this->brandLogoUpload)) {
            Storage::delete(setting()->get('brand.logo'));
            setting()->forget('brand.logo');
            $this->brandLogo = null;
        }
        if (isset($this->brandLogoUpload)) {
            $name = 'brand-logo-' . now()->format('YmdHis') . '.' . $this->brandLogoUpload->getClientOriginalExtension();
            $path = $this->brandLogoUpload->storePubliclyAs('public', $name);

            $image = new ImageResize(Storage::path($path));
            $image->resizeToHeight(24);
            $image->save(Storage::path($path));

            setting()->set('brand.logo', $path);
            $this->brandLogo = $path;
            $this->brandLogoUpload = null;
        }

        if (filled($this->customerIdNumberPattern)) {
            setting()->set('customer.id_number_pattern', $this->customerIdNumberPattern);
        } else {
            setting()->forget('customer.id_number_pattern');
        }
        if (filled($this->customerIdNumberExample)) {
            setting()->set('customer.id_number_example', $this->customerIdNumberExample);
        } else {
            setting()->forget('customer.id_number_example');
        }

        session()->flash('submitMessage', 'Settings saved.');
    }

    public function updatedBrandLogoUpload()
    {
        $this->validate([
            'brandLogoUpload' => [
                'image',
                'max:4096',
            ],
        ]);
    }

    public function getDefaultLocaleProperty()
    {
        return config('app.fallback_locale');
    }
}
