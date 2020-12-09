<div>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1>
            @if($completed)Completed orders @else Orders @endif
        </h1>
        <span>
            @if($completed)
                <button
                    class="btn btn-outline-primary"
                    wire:click="$toggle('completed')"
                    wire:loading.attr="disabled">
                    <x-bi-hourglass-split wire:loading/>
                    Current orders
                </button>
            @else
                <button
                    class="btn btn-outline-primary"
                    wire:click="$toggle('completed')"
                    wire:loading.attr="disabled">
                    <x-bi-hourglass-split wire:loading/>
                    Completed orders
                </button>
            @endif
        </span>
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
        <table class="table table-bordered table-hover shadow-sm">
            <caption>{{ $orders->count() }} orders found</caption>
            <thead>
                <th>ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Products</th>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="cursor-pointer" wire:click="showOrder({{ $order->id }})">
                        <td>{{ $order->id }}</td>
                        <td>
                            {{ $order->created_at->isoFormat('LLLL') }}<br>
                            <small>{{ $order->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <strong>Name:</strong> {{ $order->customer_name }}<br>
                            <strong>ID Number:</strong> {{ $order->customer_id_number }}<br>
                            <strong>Phone:</strong> {{ $order->customer_phone }}
                        </td>
                        <td>
                            @foreach($order->products as $product)
                                <strong>{{ $product->pivot->amount }}</strong> {{ $product->name }}<br>
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
</div>
