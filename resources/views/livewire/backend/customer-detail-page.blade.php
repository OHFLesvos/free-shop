<div class="medium-container">
    <x-card :title="$customer->name" no-footer-padding>
        <dl class="row mb-2 mt-3">
            <dt class="col-sm-3">ID number</dt>
            <dd class="col-sm-9">{{ $customer->id_number }}</dd>
            @isset($customer->locale)
                <dt class="col-sm-3">Language</dt>
                <dd class="col-sm-9">
                    @isset(config('app.supported_languages')[$customer->locale])
                        {{  config('app.supported_languages')[$customer->locale] }} ({{ strtoupper($customer->locale) }})
                    @else
                        {{ strtoupper($customer->locale) }}
                    @endisset
                </dd>
            @endisset
            <dt class="col-sm-3">Phone</dt>
            <dd class="col-sm-9">
                <x-phone-info :value="$customer->phone"/>
                <div class="d-grid gap-2 d-md-block mt-1">
                    <x-phone-number-link
                        :value="$customer->phone"
                        class="btn btn-outline-primary btn-sm">
                        <x-icon icon="phone"/> Call
                    </x-phone-number-link>
                    <x-phone-number-link
                        :value="$customer->phone"
                        :body="'Hello '.$customer->name. '. '"
                        type="sms"
                        class="btn btn-outline-primary btn-sm">
                        <x-icon icon="sms"/> SMS
                    </x-phone-number-link>
                    <x-phone-number-link
                        :value="$customer->phone"
                        :body="'Hello '.$customer->name.'. '"
                        type="whatsapp"
                        class="btn btn-outline-primary btn-sm">
                        <x-icon icon="whatsapp" type="brands"/> WhatsApp
                    </x-phone-number-link>
                    <x-phone-number-link
                        :value="$customer->phone"
                        :body="'Hello '.$customer->name.'. '"
                        type="viber"
                        class="btn btn-outline-primary btn-sm">
                        <x-icon icon="viber" type="brands"/> Viber
                    </x-phone-number-link>
                </div>
            </dd>
            <dt class="col-sm-3">Credit</dt>
            <dd class="col-sm-9">{{ $customer->credit }}</dd>
            @isset($customer->remarks)
                <dt class="col-sm-3">Remarks</dt>
                <dd class="col-sm-9">{!! nl2br(e($customer->remarks)) !!}</dd>
            @endisset
            @if($customer->is_disabled)
                <dt class="col-sm-3">Disabled</dt>
                <dd class="col-sm-9">{{ $customer->disabled_reason ?? 'Yes' }}</dd>
            @endif
            <dt class="col-sm-3">Registered</dt>
            <dd class="col-sm-9"><x-date-time-info :value="$customer->created_at"/></dd>
        </dl>
    </x-card>

    {{-- Orders --}}
    @if($customer->orders->isNotEmpty())
        <h3>Orders</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm bg-white">
                <thead>
                    <tr>
                        <th class="text-end">Order</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-end">Products</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr
                            onclick="window.location='{{ route('backend.orders.show', $order) }}'"
                            class="cursor-pointer">
                            <td class="fit text-end">#{{ $order->id }}</td>
                            <td class="fit"><x-order-status-label :order="$order" /></td>
                            <td>
                                <x-date-time-info :value="$order->created_at" />
                            </td>
                            <td class="fit text-end">
                                {{ $order->products->map(fn ($product) => $product->pivot->quantity)->sum() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="overflow-auto">{{ $orders->onEachSide(2)->links() }}</div>
    @endif
    <div class="d-flex justify-content-between mb-3">
        <span>
            @can('update', $customer)
                <a
                    href="{{ route('backend.customers.edit', $customer) }}"
                    class="btn btn-primary">Edit</a>
            @endcan
        </span>
        <a
            href="{{ route('backend.customers') }}"
            class="btn btn-link">Back to overview</a>
    </div>
</div>
