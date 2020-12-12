<div>
    <div class="d-md-flex justify-content-between align-items-center">
        <h1 class="mb-3">Orders</h1>
        <div class="btn-group mb-3" role="group">
            <button
                type="button"
                class="btn @if($status == 'open') btn-primary @else btn-outline-primary @endif"
                wire:click="$set('status', 'open')"
                wire:loading.attr="disabled">Open</button>
            <button
                type="button"
                class="btn @if($status == 'completed') btn-primary @else btn-outline-primary @endif"
                wire:click="$set('status', 'completed')"
                wire:loading.attr="disabled">Completed</button>
            <button
                type="button"
                class="btn @if($status == 'cancelled') btn-primary @else btn-outline-primary @endif"
                wire:click="$set('status', 'cancelled')"
                wire:loading.attr="disabled">Canceled</button>
        </div>
    </div>
    <div class="form-group">
        <input
            type="search"
            wire:model="search"
            placeholder="Search orders..."
            wire:keydown.escape="$set('search', '')"
            class="form-control" />
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <caption>{{ $orders->total() }} orders found</caption>
            <thead>
                <th>ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Products</th>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr data-href="{{ route('backend.orders.show', $order) }}">
                        <td>{{ $order->id }}</td>
                        <td>
                            @if($order->cancelled_at !== null)
                                {{ $order->cancelled_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                <small>{{ $order->cancelled_at->diffForHumans() }}</small>
                            @elseif($order->completed_at !== null)
                                {{ $order->completed_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                <small>{{ $order->completed_at->diffForHumans() }}</small>
                            @else
                                {{ $order->created_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                <small>{{ $order->created_at->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>Name:</strong> {{ $order->customer_name }}<br>
                            <strong>ID Number:</strong> {{ $order->customer_id_number }}<br>
                            <strong>Phone:</strong> {{ $order->customer_phone }}
                        </td>
                        <td>
                            @foreach($order->products->sortBy('name') as $product)
                                <strong>{{ $product->pivot->quantity }}</strong> {{ $product->name }}<br>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            <em>No orders found.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
</div>
