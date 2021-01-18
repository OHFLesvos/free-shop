<div>
    <h1 class="mb-3">Customer {{ $customer->name }}</h1>
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between">
            Customer details
            <span>
                ID:
                {{ $customer->id_number }}
            </span>
        </div>
        <ul class="list-group list-group-flush">
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
                <div class="mt-2">
                    <x-phone-number-link
                        :value="$customer->phone"
                        class="btn btn-primary btn-sm">
                        <x-icon icon="phone"/> Call
                    </x-phone-number-link>
                    <x-phone-number-link
                        :value="$customer->phone"
                        :body="'Hello '.$customer->name. '. '"
                        type="sms"
                        class="btn btn-primary btn-sm">
                        <x-icon icon="sms"/> SMS
                    </x-phone-number-link>
                    <x-phone-number-link
                        :value="$customer->phone"
                        :body="'Hello '.$customer->name.'. '"
                        type="whatsapp"
                        class="btn btn-primary btn-sm">
                        <x-icon icon="whatsapp" type="brands"/> WhatsApp
                    </x-phone-number-link>
                    <x-phone-number-link
                        :value="$customer->phone"
                        :body="'Hello '.$customer->name.'. '"
                        type="viber"
                        class="btn btn-primary btn-sm">
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
    </div>
    {{-- Orders --}}
    @if($customer->orders->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header">Orders</div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover m-0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Products</th>
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
                                <td>
                                    @foreach($relatedOrder->products->sortBy('name') as $product)
                                        <strong>{{ $product->pivot->quantity }}</strong> {{ $product->name }}<br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <div class="d-md-flex justify-content-between">
        <a
            href="{{ route('backend.customers') }}"
            class="btn btn-outline-primary mb-3">
            Back to customers
        </a>
        <a
            href="{{ route('backend.customers.edit', $customer) }}"
            class="btn btn-primary mb-3">
            Edit
        </a>
    </div>
</div>
