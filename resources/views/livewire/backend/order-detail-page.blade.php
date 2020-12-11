<div>
    <h1 class="mb-3">Order #{{ $order->id }}</h1>
    <ul class="list-group mb-4 shadow-sm">
        <li class="list-group-item">
            <strong>Ordered:</strong> {{ $order->created_at->isoFormat('LLLL') }}
            <small class="ml-1">{{ $order->created_at->diffForHumans() }}</small>
            @isset($order->cancelled_at)<br>
                <strong>Cancelled:</strong> {{ $order->cancelled_at->isoFormat('LLLL') }}
                <small class="ml-1">{{ $order->cancelled_at->diffForHumans() }}</small>
            @endisset
            @isset($order->completed_at)<br>
                <strong>Completed:</strong> {{ $order->completed_at->isoFormat('LLLL') }}
                <small class="ml-1">{{ $order->completed_at->diffForHumans() }}</small>
            @endisset
        </li>
        <li class="list-group-item">
            <strong>Name:</strong>
            {{ $order->customer_name }}<br>
            <strong>ID Number:</strong>
            {{ $order->customer_id_number }}<br>
            <strong>Phone:</strong>
            {{ $order->customer_phone }}
            <div class="mt-2">
                <x-phone-number-link
                    :value="$order->customer_phone"
                    class="btn btn-primary btn-sm">
                    Call
                </x-phone-number-link>
                <x-phone-number-link
                    :value="$order->customer_phone"
                    :body="'Hello '.$order->customer_name. '. '"
                    type="sms"
                    class="btn btn-primary btn-sm">
                    SMS
                </x-phone-number-link>
                <x-phone-number-link
                    :value="$order->customer_phone"
                    :body="'Hello '.$order->customer_name.'. '"
                    type="whatsapp"
                    class="btn btn-primary btn-sm">
                    WhatsApp
                </x-phone-number-link>
            </div>
        </li>
        <li class="list-group-item">
            @php
                $hostname = gethostbyaddr($order->customer_ip_address);
            @endphp
            <strong>IP Address:</strong> {{ $order->customer_ip_address }}@if($hostname != $order->customer_ip_address), {{ $hostname }}@endif
            @if(!$order->geoIpLocation->default)
            ({{ $order->geoIpLocation->city }}, @isset($order->geoIpLocation->state){{ $order->geoIpLocation->state }},@endisset {{ $order->geoIpLocation->country }})
            @endif
            <br>
            <strong>User Agent:</strong> {{ $order->UA->browser() }} {{ $order->UA->browserVersion() }} on {{ $order->UA->platform() }}<br>
        </li>
        @isset($order->remarks)
            <li class="list-group-item">
                <strong>Remarks:</strong><br>{!! nl2br(e($order->remarks)) !!}
            </li>
        @endisset
    </ul>
    <div class="card shadow-sm mb-4">
        <div class="card-header">Products</div>
        <div class="table-responsive">
            <table class="table table-bordered m-0">
                <tbody>
                    @php
                        $hasPictures = $order->products->whereNotNull('pictureUrl')->isNotEmpty();
                    @endphp
                    @foreach($order->products as $product)
                        <tr>
                            @if($hasPictures)
                                <td class="fit">
                                    @isset($product->pictureUrl)
                                        <img
                                            src="{{ $product->pictureUrl }}"
                                            alt="Product Image"
                                            style="max-width: 100px; max-height: 75px"/>
                                    @endisset
                                </td>
                            @endif
                            <td>
                                {{ $product->name }}<br>
                                <small>{{ $product->category }}</small>
                            </td>
                            <td class="fit text-right">
                                <strong><big>{{ $product->pivot->amount }}</big></strong>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($relatedOrders->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header">Related orders</div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover m-0">
                    <tbody>
                        @foreach($relatedOrders as $relatedOrder)
                            <tr data-href="{{ route('backend.orders.show', $relatedOrder) }}">
                                <td>{{ $relatedOrder->id }}</td>
                                <td>
                                    {{ $relatedOrder->created_at->isoFormat('LLLL') }}<br>
                                    <small>{{ $relatedOrder->created_at->diffForHumans() }}</small>
                                    @isset($relatedOrder->cancelled_at)
                                        <br><br>Cancelled:<br>
                                        {{ $relatedOrder->cancelled_at->isoFormat('LLLL') }}<br>
                                        <small>{{ $relatedOrder->cancelled_at->diffForHumans() }}</small>
                                    @endif
                                    @isset($relatedOrder->completed_at)
                                        <br><br>Completed:<br>
                                        {{ $relatedOrder->completed_at->isoFormat('LLLL') }}<br>
                                        <small>{{ $relatedOrder->completed_at->diffForHumans() }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>Name:</strong> {{ $relatedOrder->customer_name }}<br>
                                    <strong>ID Number:</strong> {{ $relatedOrder->customer_id_number }}<br>
                                    <strong>Phone:</strong> {{ $relatedOrder->customer_phone }}
                                </td>
                                <td>
                                    @foreach($relatedOrder->products as $product)
                                        <strong>{{ $product->pivot->amount }}</strong> {{ $product->name }}<br>
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
            href="{{ route('backend.orders') }}"
            class="btn btn-outline-primary mb-3">Back to orders</a>
        @if($order->cancelled_at !== null)
            Cancelled
        @elseif($order->completed_at !== null)
            Completed
        @else
            <span>
                <button
                    type="button"
                    class="btn btn-danger mb-3"
                    wire:click="cancel"
                    wire:loading.attr="disabled">
                    <x-bi-hourglass-split wire:loading/>
                    Cancel order
                </button>
                <button
                    type="button"
                    class="btn btn-primary mb-3"
                    wire:click="complete"
                    wire:loading.attr="disabled">
                    <x-bi-hourglass-split wire:loading/>
                    Mark as completed
                </button>
            </span>
        @endisset
    </div>
</div>
