<div class="medium-container">
    <x-card :title="$customer->name">
        <x-slot name="addon">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>ID:</strong>
                    {{ $customer->id_number }}
                </li>
                <li class="list-group-item">
                    @isset($customer->locale)
                        <strong>Language:</strong>
                        @isset(config('app.supported_languages')[$customer->locale])
                            {{ config('app.supported_languages')[$customer->locale] }}
                            ({{ strtoupper($customer->locale) }})
                        @else
                            {{ strtoupper($customer->locale) }}
                        @endif
                        <br>
                    @endisset
                    <strong>Phone:</strong>
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
                </li>
                <li class="list-group-item">
                    <strong>Credit:</strong> {{ $customer->credit }}
                </li>
                @isset($customer->remarks)
                    <li class="list-group-item">
                        <strong>Remarks:</strong><br>{!! nl2br(e($customer->remarks)) !!}
                    </li>
                @endisset
                <li class="list-group-item">
                    <strong>Registered:</strong>
                    <x-date-time-info :value="$customer->created_at"/>
                </li>
            </ul>
        </x-slot>
    </x-card>

    {{-- Orders --}}
    @if($customer->orders->isNotEmpty())
        <h2>Orders</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm bg-white">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-end">Products</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->orders as $relatedOrder)
                        <tr
                            onclick="window.location='{{ route('backend.orders.show', $relatedOrder) }}'"
                            class="cursor-pointer">
                            <td class="fit">#{{ $relatedOrder->id }}</td>
                            <td class="fit"><x-order-status-label :order="$relatedOrder" /></td>
                            <td>
                                <x-date-time-info :value="$relatedOrder->created_at" />
                            </td>
                            <td class="fit text-end">
                                {{ $relatedOrder->products->map(fn ($product) => $product->pivot->quantity)->sum() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <div class="mb-3 d-flex justify-content-between">
        <a
            href="{{ route('backend.customers.edit', $customer) }}"
            class="btn btn-primary">
            Edit
        </a>
        <a
            href="{{ route('backend.customers') }}"
            class="btn btn-link">
            Back to overview
        </a>
    </div>
</div>
