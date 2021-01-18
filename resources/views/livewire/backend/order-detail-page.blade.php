<div>
    @if ($shouldReady)
        <h1 class="mb-3">Ready Order</h1>
        <p>Should the order <strong>#{{ $order->id }}</strong> be marked as ready?</p>
        <p class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-primary" wire:loading.attr="disabled"
                wire:click="$toggle('shouldReady')">
                No
            </button>
            <button type="button" class="btn btn-success" wire:target="ready" wire:loading.attr="disabled"
                wire:click="ready">
                <x-spinner wire:loading wire:target="ready" />
                Yes
            </button>
        </p>
    @elseif ($shouldComplete)
        <h1 class="mb-3">Complete Order</h1>
        <p>Should the order <strong>#{{ $order->id }}</strong> be marked as completed?</p>
        <p class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-primary" wire:loading.attr="disabled"
                wire:click="$toggle('shouldComplete')">
                No
            </button>
            <button type="button" class="btn btn-success" wire:target="complete" wire:loading.attr="disabled"
                wire:click="complete">
                <x-spinner wire:loading wire:target="complete" />
                Yes
            </button>
        </p>
    @elseif($shouldCancel)
        <h1 class="mb-3">Cancel Order</h1>
        <p>Do you really want to cancel the order <strong>#{{ $order->id }}</strong> of
            <strong>{{ $order->customer->name }}</strong>?
        </p>
        <p class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-primary" wire:loading.attr="disabled"
                wire:click="$toggle('shouldCancel')">
                No
            </button>
            <button type="button" class="btn btn-danger" wire:target="cancel" wire:loading.attr="disabled"
                wire:click="cancel">
                <x-spinner wire:loading wire:target="cancel" />
                Yes
            </button>
        </p>
    @else
        <h1 class="mb-3">Order #{{ $order->id }}</h1>
        @if (session()->has('error'))
            <x-alert type="danger" dismissible>{{ session()->get('error') }}</x-alert>
        @endif
        @if (session()->has('message'))
            <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
        @endif
        {{-- Order details --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between">
                Order details
                <span>
                    Status: <x-order-status-label :order="$order" />
                </span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>Customer:</strong>
                    <a href="{{ route('backend.customers.show', $order->customer) }}">{{ $order->customer->name }}</a>
                    ({{ $order->customer->id_number }})
                </li>
                <li class="list-group-item">
                    <strong>IP Address:</strong>
                    <x-ip-info :value="$order->ip_address" />
                    <br>
                    <strong>Geo Location:</strong>
                    <x-geo-location-info :value="$order->ip_address" />
                    <br>
                    <strong>User Agent:</strong>
                    <x-user-agent-info :value="$order->user_agent" />
                </li>
                <li class="list-group-item">
                    <strong>Registered:</strong>
                    <x-date-time-info :value="$order->created_at" />
                </li>
            </ul>
        </div>
        {{-- Remarks --}}
        @isset($order->remarks)
            <x-alert type="info mb-4">
                <strong>Remarks from customer:</strong><br>
                {!! nl2br(e($order->remarks)) !!}
            </x-alert>
        @endisset
        {{-- Products --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">Products</div>
            <div class="table-responsive">
                <table class="table table-bordered m-0">
                    <thead>
                        <tr>
                            <th colspan="2">Product</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $hasPictures = $order->products->whereNotNull('pictureUrl')->isNotEmpty();
                        @endphp
                        @foreach ($order->products->sortBy('name') as $product)
                            <tr>
                                @if ($hasPictures)
                                    <td class="fit">
                                        @isset($product->pictureUrl)
                                            <img src="{{ $product->pictureUrl }}" alt="Product Image"
                                                style="max-width: 100px; max-height: 75px" />
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
        {{-- Order history --}}
        @php
        $audits = $order->audits()->with('user')->get();
        @endphp
        @if ($audits->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header">Order history</div>
                <ul class="list-group list-group-flush">
                    @foreach ($audits as $audit)
                        <li class="list-group-item">
                            On <strong>
                                <x-date-time-info :value="$audit->created_at" />
                            </strong>
                            <strong>{{ optional($audit->user)->name ?? 'Unknown' }}</strong>
                            @if ($audit->event == 'created')
                                registered the order.
                            @elseif($audit->event == 'updated')
                                updated the order and changed
                                @php
                                $modified = $audit->getModified();
                                @endphp
                                @foreach ($modified as $key => $val)
                                    <em>{{ $key }}</em> from <code>{{ $val['old'] }}</code> to
                                    <code>{{ $val['new'] }}</code>
                                    @if ($loop->last).@else,@endif
                                @endforeach
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Buttons --}}
        <div class="d-md-flex justify-content-between">
            <a href="{{ route('backend.orders') }}" class="btn btn-outline-primary mb-3">Back to orders</a>
            <div>
            @if ($order->status == 'new')
                <button type="button" class="btn btn-primary mb-3" wire:click="$toggle('shouldReady')">
                    Mark as ready
                </button>
            @endif
            @if ($order->status == 'ready')
                <button type="button" class="btn btn-primary mb-3" wire:click="$toggle('shouldComplete')">
                    Mark as completed
                </button>
            @endif
            @if (in_array($order->status, ['new', 'ready']))
                <button type="button" class="btn btn-danger mb-3" wire:click="$toggle('shouldCancel')">
                    Cancel order
                </button>
            @endif
            </div>
        </div>
@endif
</div>
