<div>
    <div class="d-md-flex justify-content-between align-items-center">
        <h1 class="mb-3">Orders</h1>
        <div class="overflow-auto">
            <div class="btn-group mb-3" role="group">
                <button type="button" class="btn @if ($status=='new' ) btn-warning @else btn-outline-warning @endif"
                    wire:click="$set('status', 'new')" wire:loading.attr="disabled">New</button>
                <button type="button" class="btn @if ($status=='ready' ) btn-info @else btn-outline-info @endif"
                    wire:click="$set('status', 'ready')" wire:loading.attr="disabled">Ready</button>
                <button type="button" class="btn @if ($status=='completed' ) btn-success @else btn-outline-success @endif"
                    wire:click="$set('status', 'completed')" wire:loading.attr="disabled">Completed</button>
                <button type="button" class="btn @if ($status=='cancelled' ) btn-danger @else btn-outline-danger @endif"
                    wire:click="$set('status', 'cancelled')" wire:loading.attr="disabled">Canceled</button>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <input type="search" wire:model.debounce.500ms="search" placeholder="Search orders..."
                wire:keydown.escape="$set('search', '')" class="form-control" />
            <div class="input-group-append">
                <span class="input-group-text" wire:loading wire:target="search">
                    <x-spinner />
                </span>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <caption>{{ $orders->total() }} orders found</caption>
            <thead>
                <th>ID</th>
                <th>
                    @if (in_array($status, ['completed', 'cancelled']))
                        Updated
                    @else
                        Registered
                    @endif
                </th>
                <th>Customer</th>
                <th>Products</th>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr onclick="window.location='{{ route('backend.orders.show', $order) }}'" class="cursor-pointer">
                        <td>{{ $order->id }}</td>
                        <td>
                            @if (in_array($order->status, ['new', 'ready']))
                                {{ $order->created_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                <small>{{ $order->created_at->diffForHumans() }}</small>
                            @else
                                {{ $order->updated_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                                <small>{{ $order->updated_at->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>Name:</strong> {{ $order->customer->name }}<br>
                            <strong>ID Number:</strong> {{ $order->customer->id_number }}<br>
                            <strong>Phone:</strong> {{ $order->customer->phone }}
                        </td>
                        <td>
                            @foreach ($order->products->sortBy('name') as $product)
                                <strong>{{ $product->pivot->quantity }}</strong> {{ $product->name }}<br>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            <em>
                                @if (filled($search))
                                    No orders found for term '{{ $search }}'.
                                @else
                                    No orders found.
                                @endif
                            </em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
</div>
