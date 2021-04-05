<div class="medium-container">
    
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm @can('manage text blocks') table-hover @endcan">
            <thead>
                <th>Name</th>
                <th class="text-end">Stock</th>
                <th class="text-end">Free</th>
                <th class="text-end">Reserved</th>
                <th>Last updated</th>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}<br><small class="text-muted">{{ $product->category }}</small></td>
                        <td class="text-end">{{ $product->stock }}</td>
                        <td class="text-end">{{ $product->free_quantity }}</td>
                        <td class="text-end">{{ $product->reserved_quantity }}</td>
                        <td class="fit">
                            <x-date-time-info :value="$product->updated_at" line-break />
                        </td>
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
</div>
