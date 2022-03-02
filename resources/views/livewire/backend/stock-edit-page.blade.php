<div class="small-container">
    <form wire:submit.prevent="submit" autocomplete="off">
        <x-card title="Edit stock of '{{ $product->name }}'">
            @isset($product->description)
                <p>{{ $product->description }}</p>
            @endisset
            <div class="row">
                <div class="col-sm mb-3 mb-sm-0">
                    <label for="stockInput" class="form-label">Stock</label>
                    <input
                        type="number"
                        min="0"
                        class="form-control
                        @error('product.stock') is-invalid @enderror"
                        style="width: 6em"
                        required
                        id="stockInput"
                        autocomplete="off"
                        wire:model="product.stock">
                    @error('product.stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-sm mb-3 mb-sm-0">
                    <label for="freeQuantityInput" class="form-label">Free Quantity</label>
                    <input
                        type="number"
                        class="form-control
                        @error('freeQuantity') is-invalid @enderror"
                        style="width: 6em"
                        id="freeQuantityInput"
                        autocomplete="off"
                        wire:model="freeQuantity">
                    @error('freeQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-sm">
                    <label class="form-label d-block">Reserved Quantity</label>
                    {{ $product->reserved_quantity }}
                </div>
            </div>

            <div class="mt-3">
                <label for="descriptionInput" class="form-label">Description/reason for change</label>
                <textarea
                    class="form-control
                    @error('description') is-invalid @enderror"
                    id="descriptionInput"
                    autocomplete="off"
                    required
                    wire:model="description"></textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if(session()->has('stock.edit.description'))
                <small id="descriptionHelp" class="form-text text-muted">
                    Using value from last change
                </small>
                @endif
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <a
                        href="{{ route('backend.stock') }}"
                        class="btn btn-link"
                    >
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" wire:target="submit" wire:loading.attr="disabled">
                        <x-spinner wire:loading wire:target="submit" />
                        Save
                    </button>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>
