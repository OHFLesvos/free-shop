<div class="medium-container">

    @include('livewire.backend.stock-nav')

    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm table-hover">
            <thead>
                <th>Name</th>
                <th class="text-end fit">Stock</th>
                <th class="text-end fit">Free</th>
                <th class="text-end fit">Reserved</th>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr
                        onclick="window.location='{{ route('backend.stock.edit', $product) }}'"
                        class="cursor-pointer "
                    >
                        <td class="align-middle" title="{{ $product->description }}">
                            {{ $product->name }}
                            <br><small class="text-muted">{{ $product->category }}</small>
                        </td>
                        <td class="align-middle fit text-end">{{ $product->stock }}</td>
                        <td class="text-end fit align-middle">{{ $product->free_quantity }}</td>
                        <td class="text-end fit align-middle">{{ $product->reserved_quantity }}</td>
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