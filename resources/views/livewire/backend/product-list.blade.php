<div>
    <div class="d-md-flex justify-content-between align-items-center mb-2">
        <h1>Products</h1>
        <span>
            <a
                href="{{ route('backend.products.create') }}"
                class="btn btn-primary">Register</a>
            <div class="btn-group" role="group">
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
        <table class="table table-bordered table-hover shadow-sm">
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
                @foreach($products as $product)
                    <tr class="cursor-pointer @if(!$product->is_available) table-dark text-dark @endif" wire:click="editProduct({{ $product->id }})">
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
                        <td class="text-right">{{ $product->customer_limit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
