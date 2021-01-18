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
        @if(session()->has('error'))
            <x-alert type="danger" dismissible>{{ session()->get('error') }}</x-alert>
        @endif
        @if(session()->has('message'))
            <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
        @endif
        <ul class="list-group mb-4 shadow-sm">
            <li class="list-group-item">
                <strong>Ordered:</strong>
                <x-date-time-info :value="$order->created_at"/>
                @if($order->status == 'cancelled')<br>
                    <strong>Cancelled</strong>
                @endif
                @if($order->status == 'completed')<br>
                    <strong>Completed</strong>
                @endif
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
                                        @if($relatedOrder->status == 'cancelled')
                                            <br><br>Cancelled
                                        @endif
                                        @if($relatedOrder->status == 'completed')
                                            <br><br>Completed
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
            @if(in_array($order->status, ['new', 'ready']))
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
