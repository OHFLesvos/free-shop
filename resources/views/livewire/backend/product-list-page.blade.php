<div>
    <div class="d-md-flex justify-content-between align-items-center">
        <h1 class="mb-3">Products</h1>
        <span>
            <a
                href="{{ route('backend.products.create') }}"
                class="btn btn-primary mb-3">Register</a>
            <div class="btn-group mb-3" role="group">
                <button
                    type="button"
                    class="btn @if($state == 'all') btn-primary @else btn-outline-primary @endif"
                    wire:click="$set('state', 'all')"
                    wire:loading.attr="disabled">All</button>
                <button
                    type="button"
                    class="btn @if($state == 'available') btn-primary @else btn-outline-primary @endif"
                    wire:click="$set('state', 'available')"
                    wire:loading.attr="disabled">Available</button>
                <button
                    type="button"
                    class="btn @if($state == 'disabled') btn-primary @else btn-outline-primary @endif"
                    wire:click="$set('state', 'disabled')"
                    wire:loading.attr="disabled">Disabled</button>
            </div>
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <caption>{{ $products->count() }} products registered</caption>
            <thead>
                <th>Picture</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-right">Stock<br><small>(Free/Reserved)</small></th>
                <th class="text-right">Limit</th>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr
                        data-href="{{ route('backend.products.edit', $product) }}"
                        class="@if(!$product->is_available) table-dark text-dark @endif"
                    >
                        <td class="fit">
                            @isset($product->pictureUrl)
                                <img
                                    src="{{ $product->pictureUrl }}"
                                    alt="Product Image"
                                    style="max-width: 100px; max-height: 75px"/>
                            @endisset
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->description }}</td>
                        <td class="text-right">
                            {{ $product->stock_amount }}<br>
                            <small>{{ $product->free_amount }} / {{ $product->reserved_amount }}</small>
                        </td>
                        <td class="text-right">{{ $product->limit_per_order }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            <em>No products registered.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
