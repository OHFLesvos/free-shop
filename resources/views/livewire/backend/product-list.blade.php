<div>
    <h1 class="mb-3">Products</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm">
            <caption>{{ $products->count() }} products registered</caption>
            <thead>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-right">Stock<br><small>(Free/Reserved)</small></th>
                <th class="text-right">Limit</th>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="cursor-pointer" wire:click="editProduct({{ $product->id }})">
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
