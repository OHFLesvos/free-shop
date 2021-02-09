<div>
    @if(session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="d-flex justify-content-end align-items-center mb-3">
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
    </div>
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm table-hover">
            <caption>{{ $products->count() }} products registered</caption>
            <thead>
                <th>Picture</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-end">Stock<br><small>(Free/Reserved)</small></th>
                <th class="text-end">Limit</th>
                <th class="text-end">Price</th>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr
                        onclick="window.location='{{ route('backend.products.edit', $product) }}'"
                        class="cursor-pointer @if(!$product->is_available) table-secondary @endif"
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
                        <td class="text-end">
                            {{ $product->stock }}<br>
                            <small>{{ $product->free_quantity }} / {{ $product->reserved_quantity }}</small>
                        </td>
                        <td class="text-end">{{ $product->limit_per_order }}</td>
                        <td class="text-end">{{ $product->price }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <em>No products registered.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p>
        <a
            href="{{ route('backend.products.create') }}"
            class="btn btn-primary">
            Register new user
        </a>
    </p>
</div>
