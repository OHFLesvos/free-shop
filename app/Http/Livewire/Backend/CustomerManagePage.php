<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\TrimAndNullEmptyStrings;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Tag;
use App\Services\LocalizationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerManagePage extends BackendPage
{
    use AuthorizesRequests;
    use TrimAndNullEmptyStrings;

    public Customer $customer;

    public Collection $currencies;

    public ?string $phone = null;

    public string $phoneCountry;

    public Collection $balance;

    public array $tags;

    /**
     * @var array
     */
    protected $listeners = [
        'changeTags' => 'updateTags',
    ];

    protected function rules(): array
    {
        return [
            'customer.name' => 'required',
            'customer.id_number' => [
                'required',
                setting()->has('customer.id_number_pattern')
                    ? 'regex:' . setting()->get('customer.id_number_pattern')
                    : null,
                Rule::unique('customers', 'id_number')->ignore($this->customer->id),
            ],
            'phone' => [
                'nullable',
                'required_without:customer.email',
                'phone:phoneCountry,mobile',
            ],
            'phoneCountry' => 'required_with:phone',
            'customer.email' => [
                'nullable',
                'required_without:phone',
                'email',
            ],
            'balance' => [
                'array',
            ],
            'balance.*' => [
                'required',
                'integer',
                'min:0',
            ],
            'tags' => [
                'array',
            ],
            'customer.locale' => [
                'nullable',
                Rule::in(app()->make(LocalizationService::class)->getLanguageCodes()),
            ],
            'customer.remarks' => 'nullable',
            'customer.is_disabled' => 'boolean',
            'customer.disabled_reason' => [
                'required_if:customer.is_disabled,true',
            ]
        ];
    }

    public function mount(): void
    {
        if (isset($this->customer)) {
            $this->authorize('update', $this->customer);
        } else {
            $this->authorize('create', Customer::class);
        }

        $this->currencies = Currency::orderBy('name')->get();

        if (!isset($this->customer)) {
            $this->customer = new Customer();
            $this->customer->is_disabled = false;
            $this->balance = $this->currencies
                ->mapWithKeys(fn (Currency $currency) => [$currency->id => $currency->top_up_amount]);
        } else {
            $this->balance = $this->customer->currencies
                ->mapWithKeys(fn (Currency $currency) => [$currency->id => $currency->getRelationValue('pivot')->value]);
            $this->currencies->whereNotIn('id', $this->balance->keys())->each(fn (Currency $currency) => $this->balance[$currency->id] = 0);
        }

        $this->phoneCountry = setting()->get('order.default_phone_country', '');
        $this->phone = '';
        if ($this->customer->phone != null) {
            try {
                $phone = PhoneNumber::make($this->customer->phone);
                $this->phoneCountry = $phone->getCountry();
                $this->phone = $phone->formatNational();
            } catch (NumberParseException) {
                $this->phoneCountry = '';
                $this->phone = $this->customer->phone;
            }
        }

        $this->tags = $this->customer->tags->pluck('name')->toArray();
    }

    protected function title(): string
    {
        return $this->customer->exists
            ? 'Edit Customer ' . $this->customer->name
            : 'Register Customer';
    }

    public function render(): View
    {
        return parent::view('livewire.backend.customer-form', [
            'title' => $this->customer->exists ? 'Edit Customer' : 'Register Customer',
        ]);
    }

    public function submit()
    {
        $this->authorize('update', $this->customer);

        $this->validate();

        $this->customer->phone = filled($this->phone)
            ? PhoneNumber::make($this->phone, $this->phoneCountry)->formatE164()
            : null;

        if (!$this->customer->is_disabled) {
            $this->customer->disabled_reason = null;
        }
        if ($this->customer->topped_up_at == null) {
            $this->customer->topped_up_at = now();
        }

        $this->customer->save();
        $this->customer->setBalances($this->balance);

        $tags = [];
        foreach ($this->tags as $tag) {
            $tags[] = Tag::firstOrCreate([
                'name' => $tag,
            ])->id;
        }
        $this->customer->tags()->sync($tags);

        session()->flash('message', $this->customer->wasRecentlyCreated
            ? 'Customer registered.'
            : 'Customer updated.');

        return redirect()->route('backend.customers.show', $this->customer);
    }

    public function delete()
    {
        $this->authorize('delete', $this->customer);

        $this->customer->delete();

        session()->flash('message', 'Customer deleted.');

        return redirect()->route('backend.customers');
    }

    public function updateTags(array $value): void
    {
        $this->tags = $value;
    }
}
