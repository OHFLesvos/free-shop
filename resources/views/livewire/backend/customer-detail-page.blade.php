<div>
    <h1 class="mb-3">Customer {{ $customer->name }}</h1>
    <ul class="list-group mb-4 shadow-sm">
        <li class="list-group-item">
            <strong>ID Number:</strong>
            {{ $customer->id_number }}<br>
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
    @if($customer->orders->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header">Orders</div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover m-0">
                    <tbody>
                        @foreach($customer->orders as $relatedOrder)
                            <tr
                                onclick="window.location='{{ route('backend.orders.show', $relatedOrder) }}'"
                                class="cursor-pointer">
                                <td>{{ $relatedOrder->id }}</td>
                                <td>
                                    {{ $relatedOrder->created_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                    <small>{{ $relatedOrder->created_at->diffForHumans() }}</small>
                                    @isset($relatedOrder->cancelled_at)
                                        <br><br>Cancelled:<br>
                                        {{ $relatedOrder->cancelled_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                        <small>{{ $relatedOrder->cancelled_at->diffForHumans() }}</small>
                                    @endif
                                    @isset($relatedOrder->completed_at)
                                        <br><br>Completed:<br>
                                        {{ $relatedOrder->completed_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                        <small>{{ $relatedOrder->completed_at->diffForHumans() }}</small>
                                    @endif
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
