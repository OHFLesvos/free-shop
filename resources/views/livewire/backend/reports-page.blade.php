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
        {{ $customersRegistered }} customers registered.<br>
        {{ $ordersCompleted }} orders completed from {{ $customersWithCompletedOrders }} customers.<br>
        {{ $totalProductsHandedOut }} products handed out.
        @if($productsHandedOut->isNotEmpty())
            <table class="table table-bordered bg-white shadow-sm">
                <thead>
                    <th>Product</th>
                    <th class="fit text-end">Quantity</th>
                </thead>
                <tbody>
                    @foreach($productsHandedOut as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td class="fit text-end">{{ $product['quantity'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </p>
</div>
