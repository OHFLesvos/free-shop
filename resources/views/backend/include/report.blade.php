<h2 class="display-6">
    {{ $dateRangeTitle }}
</h2>
<p>
    {{ number_format($customersRegistered) }} customers registered.<br>
    {{ number_format($ordersRegistered) }} orders registered.<br>
    {{ number_format($ordersCompleted) }} orders completed for {{ number_format($customersWithCompletedOrders) }} customers.<br>
    {{ number_format($totalProductsHandedOut) }} products handed out.<br>
    @if($totalProductsHandedOut > 0)
        {{ round($averageOrderDuration, 1) }} days needed on average to complete an order.
    @endif
</p>
@if($productsHandedOut->isNotEmpty())
    <table class="table table-bordered bg-white shadow-sm">
        <thead>
            <th>
                Product
                <a href="#" wire:click="sortBy('product')"/><x-icon icon="sort"/></a>
            </th>
            <th class="fit text-end">
                Quantity
                <a href="#" wire:click="sortBy('quantity')"/><x-icon icon="sort"/></a>
            </th>
            <th class="fit text-end">Percent</th>
        </thead>
        <tbody>
            @foreach($productsHandedOut as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td class="fit text-end">
                        {{ number_format($product['quantity']) }}
                    </td>
                    <td class="fit text-end">
                        {{ round($product['quantity'] / $totalProductsHandedOut * 100, 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
