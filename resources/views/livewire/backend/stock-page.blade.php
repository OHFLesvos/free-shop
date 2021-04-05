<div class="medium-container">
    
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm @can('manage text blocks') table-hover @endcan">
            <thead>
                <th>Name</th>
                <th class="text-end fit">Stock</th>
                <th class="text-end fit">Free</th>
                <th class="text-end fit">Reserved</th>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="align-middle">
                            {{ $product->name }}
                            <br><small class="text-muted">{{ $product->category }}</small>
                        </td>
                        <td class="align-middle fit text-end @unless($productId == $product->id) cursor-pointer @endunless" 
                            wire:click="startEdit({{ $product->id }},{{ $product->stock }})"
                        >
                            @if($productId == $product->id)
                                <input
                                type="number"
                                min="0"
                                class="form-control @error('quantity') is-invalid @enderror"  
                                style="width: 6em"
                                required
                                id="quantityInput"
                                autocomplete="off"
                                wire:model.defer="quantity"
                                wire:keydown.escape="cancelEdit"
                                wire:keydown.enter="submitEdit"
                                wire:loading.attr="disabled"
                                >
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @else
                                <x-spinner wire:loading wire:target="startEdit({{ $product->id }},{{ $product->stock }})"/>
                                {{ $product->stock }}
                            @endif
                        </td>
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

@push('scripts')
<script>
    Livewire.on('startEdit', () => {
        document.getElementById('quantityInput').focus();
    })
    Livewire.on('finishEdit', (message) => {
        showSnackbar(message);
    })
</script>
@endpush