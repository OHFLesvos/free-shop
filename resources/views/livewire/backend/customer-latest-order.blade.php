<div>
    @isset($lastOrder)
        <x-card title="Most recent Order">
            <dl class="row mb-2 mt-3">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">#{{ $lastOrder->id }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9"><x-order-status-label :order="$lastOrder" /></dd>
                <dt class="col-sm-3">Registered</dt>
                <dd class="col-sm-9"><x-date-time-info :value="$lastOrder->created_at" /></dd>
            </dl>
            <a href="{{ route('backend.orders.show', $lastOrder) }}" class="card-link">View order details</a>
            @if($hasMoreOrders)
                <a href="{{ route('backend.customers.show', [$customer, 'tab' => 'orders']) }}" class="card-link">Show more</a>
            @endif
            <a href="{{ route('backend.customers.registerOrder', $customer) }}" class="card-link">New order</a>
        </x-card>
    @else
        <a href="{{ route('backend.customers.registerOrder', $customer) }}" class="btn btn-primary">New order</a>
    @endisset
</div>
