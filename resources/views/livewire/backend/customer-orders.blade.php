<div>
    @if ($orders->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm bg-white">
                <thead>
                    <tr>
                        <th class="text-end">Order</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-end">Products</th>
                        <th class="text-end">Costs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr onclick="window.location='{{ route('backend.orders.show', $order) }}'"
                            class="cursor-pointer">
                            <td class="fit text-end">#{{ $order->id }}</td>
                            <td class="fit">
                                <x-order-status-label :order="$order" />
                            </td>
                            <td>
                                <x-date-time-info :value="$order->created_at" />
                            </td>
                            <td class="fit text-end">
                                {{ $order->products->map(fn($product) => $product->pivot->quantity)->sum() }}
                            </td>
                            <td class="fit text-end">
                                {{ $order->getCostsString() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="overflow-auto">{{ $orders->onEachSide(2)->links() }}</div>
    @else
        <x-alert type="info">
            No orders registered.
        </x-alert>
    @endif
    @can('create', App\Models\Order::class)
        <p><a href="{{ route('backend.customers.registerOrder', $customer) }}" class="btn btn-primary">New order</a></p>
    @endcan
</div>
