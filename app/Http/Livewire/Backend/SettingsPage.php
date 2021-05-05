<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use Illuminate\Support\Collection;
use Countries;
use Gumlet\ImageResize;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Setting;

class SettingsPage extends BackendPage
{
    use WithFileUploads;
    use AuthorizesRequests;
    use CurrentRouteName;

    public Collection $geoblockWhitelist;
    public string $orderDefaultPhoneCountry;
    public string $timezone;
    public $customerStartingCredit;
    public bool $shopDisabled;
    public bool $groupProductsByCategories;
    public $shopMaxOrdersPerDay;
    public $customerIdNumberPattern;
    public $customerIdNumberExample;

    public $customerCreditTopupDays;
    public $customerCreditTopupAmount;
    public $customerCreditTopupMaximum;

    public $countries;

    public ?string $selectedCountry = null;

    public $brandLogo;
    public $brandLogoUpload;
    public bool $brandLogoRemove = false;
    public $brandFavicon;
    public $brandFaviconUpload;
    public bool $brandFaviconRemove = false;

    public $customerWaitingTimeBetweenOrders;

    public $contentLocale;

    public bool $skipOrderRegisteredNotification;

    protected function rules() {
        return [
            'shopDisabled' => [
                'boolean',
            ],
            'groupProductsByCategories' => [
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
            'customerStartingCredit' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'customerCreditTopupDays' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'customerCreditTopupAmount' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'customerCreditTopupMaximum' => [
                'nullable',
                'integer',
                'min:1',
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
            ],
            'customerWaitingTimeBetweenOrders' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'skipOrderRegisteredNotification' => [
                'boolean',
            ],
        ];
    }

    public function mount()
    {
        $this->authorize('update settings');

        $this->shopDisabled = setting()->has('shop.disabled');
        $this->groupProductsByCategories = setting()->has('shop.group_products_by_categories');
        $this->geoblockWhitelist = collect(setting()->get('geoblock.whitelist', []));
        $this->orderDefaultPhoneCountry = setting()->get('order.default_phone_country', '');
        $this->timezone = setting()->get('timezone', '');
        $this->customerStartingCredit = setting()->get('customer.starting_credit', '');
        $this->customerCreditTopupDays = setting()->get('customer.credit_topup.days', '');
        $this->customerCreditTopupAmount = setting()->get('customer.credit_topup.amount', '');
        $this->customerCreditTopupMaximum = setting()->get('customer.credit_topup.maximum', '');
        $this->shopMaxOrdersPerDay = setting()->get('shop.max_orders_per_day', '');
        $this->brandLogo = setting()->get('brand.logo');
        $this->brandFavicon = setting()->get('brand.favicon');
        $this->customerIdNumberPattern = setting()->get('customer.id_number_pattern', '');
        $this->customerIdNumberExample = setting()->get('customer.id_number_example', '');
        $this->customerWaitingTimeBetweenOrders = setting()->get('customer.waiting_time_between_orders', '');
        $this->skipOrderRegisteredNotification = setting()->has('customer.skip_order_registered_notification');

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
        $this->authorize('update settings');

        $this->validate();

        $checksum = md5(json_encode(Setting::all()));

        if ($this->shopDisabled) {
            setting()->set('shop.disabled', true);
        } else {
            setting()->forget('shop.disabled');
        }
        if ($this->groupProductsByCategories) {
            setting()->set('shop.group_products_by_categories', true);
        } else {
            setting()->forget('shop.group_products_by_categories');
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

        if (filled($this->customerStartingCredit)) {
            setting()->set('customer.starting_credit', $this->customerStartingCredit);
        } else {
            setting()->forget('customer.starting_credit');
        }


        if (filled($this->customerCreditTopupDays)) {
            setting()->set('customer.credit_topup.days', $this->customerCreditTopupDays);
        } else {
            setting()->forget('customer.credit_topup.days');
        }

        if (filled($this->customerCreditTopupAmount)) {
            setting()->set('customer.credit_topup.amount', $this->customerCreditTopupAmount);
        } else {
            setting()->forget('customer.credit_topup.amount');
        }

        if (filled($this->customerCreditTopupMaximum)) {
            setting()->set('customer.credit_topup.maximum', $this->customerCreditTopupMaximum);
        } else {
            setting()->forget('customer.credit_topup.maximum');
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

        if (setting()->has('brand.favicon') && ($this->brandFaviconRemove) || isset($this->brandFaviconUpload)) {
            Storage::delete(setting()->get('brand.favicon'));
            setting()->forget('brand.favicon');
            $this->brandFavicon = null;
        }
        if (isset($this->brandFaviconUpload)) {
            $name = 'brand-favicon-' . now()->format('YmdHis') . '.' . $this->brandFaviconUpload->getClientOriginalExtension();
            $path = $this->brandFaviconUpload->storePubliclyAs('public', $name);

            $image = new ImageResize(Storage::path($path));
            $image->resizeToBestFit(32, 32);
            $image->save(Storage::path($path));

            setting()->set('brand.favicon', $path);
            $this->brandFavicon = $path;
            $this->brandFaviconUpload = null;
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

        if (filled($this->customerWaitingTimeBetweenOrders)) {
            setting()->set('customer.waiting_time_between_orders', $this->customerWaitingTimeBetweenOrders);
        } else {
            setting()->forget('customer.waiting_time_between_orders');
        }

        if ($this->skipOrderRegisteredNotification) {
            setting()->set('customer.skip_order_registered_notification', true);
        } else {
            setting()->forget('customer.skip_order_registered_notification');
        }

        if ($checksum != md5(json_encode(Setting::all()))) {
            Log::info('Updated settings.', [
                'event.kind' => 'event',
                'event.category' => 'configuration',
                'event.types' => 'change',
                'settings' => Setting::all(),
            ]);
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

    public function updatedBrandFaviconUpload()
    {
        $this->validate([
            'brandFaviconUpload' => [
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
