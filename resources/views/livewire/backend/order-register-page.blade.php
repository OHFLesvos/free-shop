<div class="medium-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">

        @if ($errors->any())
            <x-alert type="danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </x-alert>
        @endif

        <x-card title="Register order for {{ $customer->name }}">
            @foreach ($products as $product)
                <div class="row mb-3">
                    <label
                        for="product{{ $product->id }}Input"
                        class="col-sm-10 col-form-label">
                        {{ $product->name }}
                        <br>
                        <small class="form-text text-muted">
                            Price: {{ $product->price }}@isset($product->limit_per_order), Limit per order: {{ $product->limit_per_order }}@endisset
                        </small>
                    </label>

                    <div class="col-sm-2">
                        <input
                            type="number"
                            min="0"
                            max="{{ $product->quantity_available_for_customer }}"
                            wire:model="selection.{{ $product->id }}"
                            placeholder="0"
                            class="form-control"
                            id="product{{ $product->id }}Input">
                            <small class="form-text text-muted">
                                Stock: {{ $product->quantity_available_for_customer }}
                            </small>

                    </div>
                </div>
            @endforeach

            <p class="text-end">Total price: {{ $this->totalPrice }}</p>

            <div class="mb-3">
                <label for="remarksInput" class="form-label">Remarks</label>
                <textarea class="form-control @error('order.remarks') is-invalid @enderror" id="remarksInput"
                    autocomplete="off" rows="3" wire:model.defer="order.remarks"></textarea>
                @error('order.remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row mb-3">
                <label for="remarksInput" class="col-sm-10 col-form-label">Number of orders to place:</label>
                <div class="col-sm-2">
                <input
                    type="number"
                    min="1"
                    wire:model="numberOfOrders"
                    placeholder="1"
                    class="form-control">
                </div>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    @can('view', $customer)
                        <a href="{{ route('backend.customers.show', $customer) }}" class="btn btn-link">Cancel</a>
                    @endcan
                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:target="submit"
                        wire:loading.attr="disabled"
                        @if($order->exists) disabled @endif>
                        <x-spinner wire:loading wire:target="submit" />
                        Save
                    </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>
