<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use App\Rules\CountryCode;
use Countries;
use Gumlet\ImageResize;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\WithFileUploads;
use Setting;

class SettingsPage extends BackendPage
{
    use AuthorizesRequests;
    use CurrentRouteName;
    use WithFileUploads;

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

    public ?string $brandName;

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

    protected function rules(): array
    {
        return [
            'brandName' => [
                'nullable',
                'string',
            ],
            'shopDisabled' => [
                'boolean',
            ],
            'groupProductsByCategories' => [
                'boolean',
            ],
            'orderDefaultPhoneCountry' => [
                'nullable',
                new CountryCode,
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
                    if ((@preg_match($value, '') === false)) {
                        $fail('The pattern is invalid.');
                    }
                },
            ],
            'customerIdNumberExample' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (filled($this->customerIdNumberPattern)) {
                        $values = preg_split('/\s*,\s*/', $value);
                        foreach ($values as $testValue) {
                            if (! preg_match($this->customerIdNumberPattern, $testValue)) {
                                $fail('The example is invalid.');
                            }
                        }
                    }
                },
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
        $this->brandName = setting()->get('brand.name', '');
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

        $checksum = md5((string) json_encode(Setting::all()));

        $this->updateBooleanSetting('shop.disabled', $this->shopDisabled);

        $this->updateBooleanSetting('shop.group_products_by_categories', $this->groupProductsByCategories);

        $this->updateCollectionSetting('geoblock.whitelist', $this->geoblockWhitelist);

        $this->updateStringSetting('order.default_phone_country', $this->orderDefaultPhoneCountry);

        $this->updateStringSetting('timezone', $this->timezone);

        $this->updateStringSetting('customer.starting_credit', $this->customerStartingCredit);

        $this->updateStringSetting('customer.credit_top_up.days', $this->customerCreditTopUpDays);

        $this->updateStringSetting('customer.credit_top_up.amount', $this->customerCreditTopUpAmount);

        $this->updateStringSetting('customer.credit_top_up.maximum', $this->customerCreditTopUpMaximum);

        $this->updateStringSetting('shop.max_orders_per_day', $this->shopMaxOrdersPerDay);

        if (setting()->has('brand.logo') && ($this->brandLogoRemove) || isset($this->brandLogoUpload)) {
            if (filled(setting()->get('brand.logo'))) {
                Storage::delete(setting()->get('brand.logo'));
            }
            setting()->forget('brand.logo');
            $this->brandLogo = null;
            $this->brandLogoRemove = false;
        }
        if (isset($this->brandLogoUpload)) {
            $name = 'brand-logo-' . now()->format('YmdHis') . '.' . $this->brandLogoUpload->getClientOriginalExtension();
            $image = ImageResize::createFromString($this->brandLogoUpload->get());
            $image->resizeToHeight(24);
            Storage::put($name, $image->getImageAsString(), 'public');
            $path = Storage::url($name);
            //$path = $this->brandLogoUpload->storePubliclyAs('public', $name);

            if ($path) {
                //$image = new ImageResize(Storage::path($path));

                //$image->resizeToHeight(24);
                //$image->save(Storage::path($path));

                setting()->set('brand.logo', $path);
                $this->brandLogo = $path;
            }

            $this->brandLogoUpload = null;
        }

        if (setting()->has('brand.favicon') && ($this->brandFaviconRemove) || isset($this->brandFaviconUpload)) {
            if (filled(setting()->get('brand.favicon'))) {
                Storage::delete(setting()->get('brand.favicon'));
            }
            setting()->forget('brand.favicon');
            $this->brandFavicon = null;
            $this->brandFaviconRemove = false;
        }
        if (isset($this->brandFaviconUpload)) {
            $name = 'brand-favicon-' . now()->format('YmdHis') . '.' . $this->brandFaviconUpload->getClientOriginalExtension();
            $image = ImageResize::createFromString($this->brandFaviconUpload->get());
            $image->resizeToBestFit(32, 32);
            Storage::put($name, $image->getImageAsString(), 'public');
            $path = Storage::url($name);
            //$path = $this->brandFaviconUpload->storePubliclyAs('public', $name);

            if ($path) {
                //$image = new ImageResize(Storage::path($path));
                //$image->resizeToBestFit(32, 32);
                //$image->save(Storage::path($path));

                setting()->set('brand.favicon', $path);
                $this->brandFavicon = $path;
            }

            $this->brandFaviconUpload = null;
        }

        $this->updateStringSetting('brand.name', $this->brandName);

        $this->updateStringSetting('customer.id_number_pattern', $this->customerIdNumberPattern);

        $this->updateStringSetting('customer.id_number_example', $this->customerIdNumberExample);

        $this->updateStringSetting('customer.waiting_time_between_orders', $this->customerWaitingTimeBetweenOrders);

        $this->updateBooleanSetting('customer.skip_order_registered_notification', $this->skipOrderRegisteredNotification);

        setting()->save();

        if ($checksum != md5((string) json_encode(Setting::all()))) {
            Log::info('Updated settings.', [
                'event.kind' => 'event',
                'event.category' => 'configuration',
                'event.types' => 'change',
                'settings' => Setting::all(),
            ]);
        }

        session()->flash('submitMessage', 'Settings saved.');
    }

    private function updateStringSetting(string $key, ?string $value): void
    {
        if (filled($value)) {
            setting()->set($key, $value);

            return;
        }
        setting()->forget($key);
    }

    private function updateBooleanSetting(string $key, bool $value): void
    {
        if ($value) {
            setting()->set($key, true);

            return;
        }
        setting()->forget($key);
    }

    private function updateCollectionSetting(string $key, Collection $value): void
    {
        if ($value->isNotEmpty()) {
            setting()->set($key, $value->toArray());

            return;
        }
        setting()->forget($key);
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
