<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use Illuminate\Support\Collection;
use Countries;
use Gumlet\ImageResize;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\WithFileUploads;
use Setting;

class SettingsPage extends BackendPage
{
    use WithFileUploads;
    use AuthorizesRequests;
    use CurrentRouteName;

    protected string $title = 'Settings';

    public Collection $geoblockWhitelist;

    public string $orderDefaultPhoneCountry;

    public string $timezone;

    public string $customerStartingCredit;

    public bool $shopDisabled;

    public bool $groupProductsByCategories;

    public string $shopMaxOrdersPerDay;

    public string $customerIdNumberPattern;

    public string $customerIdNumberExample;

    public string $customerCreditTopUpDays;

    public string $customerCreditTopUpAmount;

    public string $customerCreditTopUpMaximum;

    public string $customerWaitingTimeBetweenOrders;

    public Collection $countries;

    public ?string $selectedCountry = null;

    public ?string $brandLogo;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $brandLogoUpload;

    public bool $brandLogoRemove = false;

    public ?string $brandFavicon;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $brandFaviconUpload;

    public bool $brandFaviconRemove = false;

    public bool $skipOrderRegisteredNotification;

    protected function rules(): array {
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
            'customerCreditTopUpDays' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'customerCreditTopUpAmount' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'customerCreditTopUpMaximum' => [
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

    public function mount(): void
    {
        $this->authorize('update settings');

        $this->shopDisabled = setting()->has('shop.disabled');
        $this->groupProductsByCategories = setting()->has('shop.group_products_by_categories');
        $this->geoblockWhitelist = collect(setting()->get('geoblock.whitelist', []));
        $this->orderDefaultPhoneCountry = setting()->get('order.default_phone_country', '');
        $this->timezone = setting()->get('timezone', '');
        $this->customerStartingCredit = setting()->get('customer.starting_credit', '');
        $this->customerCreditTopUpDays = setting()->get('customer.credit_top_up.days', '');
        $this->customerCreditTopUpAmount = setting()->get('customer.credit_top_up.amount', '');
        $this->customerCreditTopUpMaximum = setting()->get('customer.credit_top_up.maximum', '');
        $this->shopMaxOrdersPerDay = setting()->get('shop.max_orders_per_day', '');
        $this->brandLogo = setting()->get('brand.logo');
        $this->brandFavicon = setting()->get('brand.favicon');
        $this->customerIdNumberPattern = setting()->get('customer.id_number_pattern', '');
        $this->customerIdNumberExample = setting()->get('customer.id_number_example', '');
        $this->customerWaitingTimeBetweenOrders = setting()->get('customer.waiting_time_between_orders', '');
        $this->skipOrderRegisteredNotification = setting()->has('customer.skip_order_registered_notification');

        $this->countries = collect(Countries::getList());
    }

    public function render(): View
    {
        return parent::view('livewire.backend.settings-page');
    }

    public function addToGeoblockWhitelist(): void
    {
        if (filled($this->selectedCountry) && ! $this->geoblockWhitelist->contains($this->selectedCountry)) {
            $this->geoblockWhitelist->push($this->selectedCountry);
        }
        $this->selectedCountry = null;
    }

    public function removeFromGeoblockWhitelist(string $value): void
    {
        $this->geoblockWhitelist = $this->geoblockWhitelist->filter(fn ($val) => $value != $val)->values();
    }

    public function submit(): void
    {
        $this->authorize('update settings');

        $this->validate();

        $checksum = md5((string)json_encode(Setting::all()));

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

        if (filled($this->customerCreditTopUpDays)) {
            setting()->set('customer.credit_top_up.days', $this->customerCreditTopUpDays);
        } else {
            setting()->forget('customer.credit_top_up.days');
        }

        if (filled($this->customerCreditTopUpAmount)) {
            setting()->set('customer.credit_top_up.amount', $this->customerCreditTopUpAmount);
        } else {
            setting()->forget('customer.credit_top_up.amount');
        }

        if (filled($this->customerCreditTopUpMaximum)) {
            setting()->set('customer.credit_top_up.maximum', $this->customerCreditTopUpMaximum);
        } else {
            setting()->forget('customer.credit_top_up.maximum');
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

            if ($path) {
                $image = new ImageResize(Storage::path($path));
                $image->resizeToHeight(24);
                $image->save(Storage::path($path));

                setting()->set('brand.logo', $path);
                $this->brandLogo = $path;
            }

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

            if ($path) {
                $image = new ImageResize(Storage::path($path));
                $image->resizeToBestFit(32, 32);
                $image->save(Storage::path($path));

                setting()->set('brand.favicon', $path);
                $this->brandFavicon = $path;
            }

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

        if ($checksum != md5((string)json_encode(Setting::all()))) {
            Log::info('Updated settings.', [
                'event.kind' => 'event',
                'event.category' => 'configuration',
                'event.types' => 'change',
                'settings' => Setting::all(),
            ]);
        }

        session()->flash('submitMessage', 'Settings saved.');
    }

    public function updatedBrandLogoUpload(): void
    {
        $this->validate([
            'brandLogoUpload' => [
                'image',
                'max:4096',
            ],
        ]);
    }

    public function updatedBrandFaviconUpload(): void
    {
        $this->validate([
            'brandFaviconUpload' => [
                'image',
                'max:4096',
            ],
        ]);
    }

    public function getDefaultLocaleProperty(): string
    {
        return config('app.fallback_locale');
    }
}
