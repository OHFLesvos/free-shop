<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomerManagePage extends BackendPage
{
    use AuthorizesRequests;

    public Customer $customer;

    public string $customer_phone;

    public string $customer_phone_country;

    public array $customer_tags;

    public bool $shouldDelete = false;

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
                'unique:customers,id_number,' . $this->customer->id,
            ],
            'customer_phone' => [
                'required',
                'phone:customer_phone_country,mobile',
            ],
            'customer_phone_country' => 'required_with:customer_phone',
            'customer_tags' => [
                'array',
            ],
            'customer.credit' => [
                'integer',
                'min:0',
            ],
            'customer.locale' => [
                'nullable',
                Rule::in(array_keys(config('app.supported_languages'))),
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

        if (! isset($this->customer)) {
            $this->customer = new Customer();
            $this->customer->credit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
            $this->customer->is_disabled = false;
        }

        $this->customer_phone_country = setting()->get('order.default_phone_country', '');
        $this->customer_phone = '';
        if ($this->customer->phone != null) {
            try {
                $phone = PhoneNumber::make($this->customer->phone);
                $this->customer_phone_country = $phone->getCountry();
                $this->customer_phone = $phone->formatNational();
            } catch (NumberParseException $ignored) {
                $this->customer_phone_country = '';
                $this->customer_phone = $this->customer->phone;
            }
        }

        $this->customer_tags = $this->customer->tags->pluck('name')->toArray();
    }

    protected function title(): string
    {
        return $this->customer->exists
            ? 'Edit Customer ' . $this->customer->name
            : 'Register Customer';
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return parent::view('livewire.backend.customer-form', [
            'title' => $this->customer->exists ? 'Edit Customer' : 'Register Customer',
        ]);
    }

    /**
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function submit()
    {
        $this->authorize('update', $this->customer);

        $this->validate();

        $this->customer->phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();

        if (!$this->customer->is_disabled) {
            $this->customer->disabled_reason = null;
        }

        $this->customer->save();

        $tags = [];
        foreach ($this->customer_tags as $tag) {
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

    /**
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function delete()
    {
        $this->authorize('delete', $this->customer);

        $this->customer->delete();

        session()->flash('message', 'Customer deleted.');

        return redirect()->route('backend.customers');
    }

    public function updateTags(array $value): void
    {
        $this->customer_tags = $value;
    }
}
