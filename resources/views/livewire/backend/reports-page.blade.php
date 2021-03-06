<div class="medium-container">
    <div class="input-group mb-3" style="max-width: 30em">
        <button
            class="btn btn-outline-secondary dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">{{ $ranges[$range] ?? 'Date range' }}</button>
        <ul class="dropdown-menu">
            @foreach($ranges as $key => $label)
                <li>
                    <button
                        class="dropdown-item @if($range == $key) active @endif"
                        type="button"
                        wire:click="$set('range', '{{ $key }}')">
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>
        <input
            type="date"
            wire:model.lazy="date_start"
            class="form-control w-auto"
            @isset($date_end) max="{{ $date_end }}" @endisset
        />
        <input
            type="date"
            wire:model.lazy="date_end"
            class="form-control w-auto"
            @isset($date_start) min="{{ $date_start }}" @endisset
            max="{{ now()->toDateString() }}"
        />
    </div>

    <h2 class="display-6">
        {{ $this->dateRangeTitle }}
    </h2>
    <p>
        {{ number_format($customersRegistered) }} customers registered.<br>
        {{ number_format($ordersRegistered) }} orders registered.<br>
        {{ number_format($ordersCompleted) }} orders completed for {{ number_format($customersWithCompletedOrders) }} customers.<br>
        {{ number_format($totalProductsHandedOut) }} products handed out.<br>
        @if($totalProductsHandedOut > 0)
            {{ round($averageOrderDuration, 1) }} days needed on average to complete an order.
        @endif
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
    </p>
</div>
