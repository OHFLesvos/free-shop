<div class="medium-container">

    @include('livewire.backend.stock-nav')

    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif

    <form wire:submit.prevent="submit" autocomplete="off">
        <div class="table-responsive">
            <table class="table table-bordered bg-white shadow-sm">
                <thead>
                    <th>Name</th>
                    <th class="text-end fit">Stock</th>
                    <th class="fit">Add</th>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="align-middle" title="{{ $product->description }}">
                                {{ $product->name }}
                                <br><small class="text-muted">{{ $product->category }}</small>
                            </td>
                            <td class="align-middle fit text-end">{{ $product->stock }}</td>
                            <td class="fit align-middle">
                                <input
                                    type="number"
                                    placeholder="0"
                                    min="1"
                                    class="form-control
                                    @error('selection') is-invalid @enderror
                                    @error('selection.'.$product->id) is-invalid @enderror
                                    "
                                    style="width: 6em"
                                    autocomplete="off"
                                    wire:model="selection.{{ $product->id }}">
                                    @error('selection') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @error('selection.'.$product->id) <div class="invalid-feedback">{{ $message }}</div> @enderror
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

        <div class="mb-3">
            <label for="descriptionInput" class="form-label">Description/reason for change</label>
            <textarea
                class="form-control
                @error('description') is-invalid @enderror"
                id="descriptionInput"
                autocomplete="off"
                required
                wire:model="description"></textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <p class="text-end">
            <button type="submit" class="btn btn-primary" wire:target="submit" wire:loading.attr="disabled">
                <x-spinner wire:loading wire:target="submit" />
                Save
            </button>
        </p>
    </form>
</div>
