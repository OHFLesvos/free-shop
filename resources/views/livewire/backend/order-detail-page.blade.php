<div>
    @if($shouldComplete)
        <h1 class="mb-3">Complete Order</h1>
        <p>Should the order <strong>#{{ $order->id }}</strong> be marked as completed?</p>
        <p class="d-flex justify-content-between">
            <button
                type="button"
                class="btn btn-outline-primary"
                wire:loading.attr="disabled"
                wire:click="$toggle('shouldComplete')">
                No
            </button>
            <button
                type="button"
                class="btn btn-success"
                wire:target="complete"
                wire:loading.attr="disabled"
                wire:click="complete">
                <x-spinner wire:loading wire:target="complete"/>
                Yes
            </button>
        </p>
    @elseif($shouldCancel)
        <h1 class="mb-3">Cancel Order</h1>
        <p>Do you really want to cancel the order <strong>#{{ $order->id }}</strong> of <strong>{{ $order->customer->name }}</strong>?</p>
        <p class="d-flex justify-content-between">
            <button
                type="button"
                class="btn btn-outline-primary"
                wire:loading.attr="disabled"
                wire:click="$toggle('shouldCancel')">
                No
            </button>
            <button
                type="button"
                class="btn btn-danger"
                wire:target="cancel"
                wire:loading.attr="disabled"
                wire:click="cancel">
                <x-spinner wire:loading wire:target="cancel"/>
                Yes
            </button>
        </p>
    @else
        <h1 class="mb-3">Order #{{ $order->id }}</h1>
        <ul class="list-group mb-4 shadow-sm">
            <li class="list-group-item">
                <strong>Ordered:</strong>
                <x-date-time-info :value="$order->created_at"/>
                @isset($order->cancelled_at)<br>
                    <strong>Cancelled:</strong>
                    <x-date-time-info :value="$order->cancelled_at"/>
                @endisset
                @isset($order->completed_at)<br>
                    <strong>Completed:</strong>
                    <x-date-time-info :value="$order->completed_at"/>
                @endisset
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm">
                        <strong>Name:</strong>
                        {{ $order->customer->name }}<br>
                        <strong>ID Number:</strong>
                        {{ $order->customer->id_number }}<br>
                        @isset($order->locale)
                            <strong>Language:</strong>
                            @isset(config('app.supported_languages')[$order->customer->locale])
                                {{ config('app.supported_languages')[$order->customer->locale] }}
                                ({{ strtoupper($order->locale) }})
                            @else
                                {{ strtoupper($order->locale) }}
                            @endif
                            <br>
                        @endisset
                        <strong>Phone:</strong>
                        {{ $order->customer->phone }}
                    </div>
                    <div class="col-sm-auto mt-2 mt-sm-1">
                        <a
                            href="{{ route('backend.customers.show', $order->customer) }}"
                            class="btn btn-primary">
                            View
                        </a>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <strong>IP Address:</strong>
                <x-ip-info :value="$order->ip_address"/>
                <br>
                <strong>Geo Location:</strong>
                <x-geo-location-info :value="$order->ip_address"/>
                <br>
                <strong>User Agent:</strong>
                <x-user-agent-info :value="$order->user_agent"/>
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
                        @foreach($order->products->sortBy('name') as $product)
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
                                <td class="fit text-right align-middle">
                                    <strong><big>{{ $product->pivot->quantity }}</big></strong>
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
                                        <strong>Name:</strong> {{ $relatedOrder->customer->name }}<br>
                                        <strong>ID Number:</strong> {{ $relatedOrder->customer->id_number }}<br>
                                        <strong>Phone:</strong> {{ $relatedOrder->customer->phone }}
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
                href="{{ route('backend.orders') }}"
                class="btn btn-outline-primary mb-3">Back to orders</a>
            @if($order->cancelled_at == null && $order->completed_at == null)
                <div>
                    <button
                        type="button"
                        class="btn btn-danger mb-3"
                        wire:click="$toggle('shouldCancel')">
                        Cancel order
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary mb-3"
                        wire:click="$toggle('shouldComplete')">
                        Mark as completed
                    </button>
                </div>
            @endisset
        </div>
    @endif
</div>
