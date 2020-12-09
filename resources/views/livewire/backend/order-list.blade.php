<div>
    <h1>Orders</h1>
    <div class="form-group">
        <input
            type="search"
            wire:model="search"
            placeholder="Search orders..."
            wire:keydown.escape="$set('search', '')"
            class="form-control" />
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <th>Date</th>
                <th>Customer</th>
                <th>Products</th>
                <th>Remarks</th>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            {{ $order->created_at->isoFormat('LLLL') }}<br>
                            <small>{{ $order->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <strong>Name:</strong> {{ $order->customer_name }}<br>
                            <strong>ID Number:</strong> {{ $order->customer_id_number }}<br>
                            <strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
                        </td>
                        <td>
                            @foreach($order->products as $product)
                                <strong>{{ $product->pivot->amount }}</strong> {{ $product->name }}<br>
                            @endforeach
                        </td>
                        <td style="max-width: 20em">{{ $order->remarks }}</td>
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
