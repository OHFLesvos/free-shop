<div class="medium-container">
    <x-card :title="$customer->name" no-footer-padding>
        <dl class="row mb-2 mt-3">
            <dt class="col-sm-3">ID number</dt>
            <dd class="col-sm-9">{{ $customer->id_number }}</dd>
            @isset($customer->locale)
                <dt class="col-sm-3">Language</dt>
                <dd class="col-sm-9">
                    @inject('localization', 'App\Services\LocalizationService')
                    {{ $localization->getLanguageName($customer->locale) }}
                </dd>
            @endisset
            @isset($customer->phone)
                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">
                    <x-phone-info :value="$customer->phone" />
                    <div class="d-grid gap-2 d-md-block mt-1">
                        <x-phone-number-link :value="$customer->phone" class="btn btn-outline-primary btn-sm">
                            <x-icon icon="phone" /> Call
                        </x-phone-number-link>
                        <x-phone-number-link :value="$customer->phone" :body="'Hello '.$customer->name. '. '" type="sms"
                            class="btn btn-outline-primary btn-sm">
                            <x-icon icon="sms" /> SMS
                        </x-phone-number-link>
                        <x-phone-number-link :value="$customer->phone" :body="'Hello '.$customer->name.'. '" type="whatsapp"
                            class="btn btn-outline-primary btn-sm">
                            <x-icon icon="whatsapp" type="brands" /> WhatsApp
                        </x-phone-number-link>
                        <x-phone-number-link :value="$customer->phone" :body="'Hello '.$customer->name.'. '" type="viber"
                            class="btn btn-outline-primary btn-sm">
                            <x-icon icon="viber" type="brands" /> Viber
                        </x-phone-number-link>
                    </div>
                </dd>
            @endisset
            @isset($customer->email)
                <dt class="col-sm-3">Email address</dt>
                <dd class="col-sm-9"><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></dd>
            @endisset
            <dt class="col-sm-3">Balance</dt>
            <dd class="col-sm-9">{!! nl2br(e($customer->balance()->map(fn ($v, $k) => "$v $k")->join("\n") )) !!}</dd>
            @isset($customer->topped_up_at)
                <dt class="col-sm-3">Last top-up</dt>
                <dd class="col-sm-9">{{ $customer->topped_up_at->isoFormat('LL') }}</dd>
            @endif
            @isset($customer->nextTopUpDate)
                <dt class="col-sm-3">Next top-up</dt>
                <dd class="col-sm-9">{{ $customer->nextTopUpDate->isoFormat('LL') }}</dd>
            @endif
            @isset($customer->remarks)
                <dt class="col-sm-3">Remarks</dt>
                <dd class="col-sm-9">{!! nl2br(e($customer->remarks)) !!}</dd>
            @endisset
            <dt class="col-sm-3">Tags</dt>
            <dd class="col-sm-9">
                @livewire('backend.customer-tags', ['customer' => $customer])
            </dd>
            @if ($customer->is_disabled)
                <dt class="col-sm-3">Disabled</dt>
                <dd class="col-sm-9">{{ $customer->disabled_reason ?? 'Yes' }}</dd>
            @endif
            <dt class="col-sm-3">Registered</dt>
            <dd class="col-sm-9">
                <x-date-time-info :value="$customer->created_at" />
            </dd>
        </dl>

        <x-slot name="footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('backend.customers') }}" class="btn btn-link">Back to overview</a>
                <span>
                    @can('update', $customer)
                        <a href="{{ route('backend.customers.edit', $customer) }}" class="btn btn-primary">Edit</a>
                    @endcan
                </span>
            </div>
        </x-slot>
    </x-card>

    @livewire('backend.customer-comments', ['customer' => $customer])
    @livewire('backend.customer-orders', ['customer' => $customer])
    @livewire('backend.customer-history', ['customer' => $customer])

</div>
