<div class="medium-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card title="Register order">
            <p><strong>Customer:</strong> {{ $customer->name }}</p>
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="remarksInput" class="form-label">Remarks</label>
                    <textarea
                        class="form-control @error('order.remarks') is-invalid @enderror"
                        id="remarksInput"
                        autocomplete="off"
                        rows="3"
                        wire:model.defer="order.remarks"></textarea>
                    @error('order.remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                        @can('view', $customer)
                            <a
                                href="{{ route('backend.customers.show', $customer) }}"
                                class="btn btn-link">Cancel</a>
                        @endcan
                        <button
                            type="submit"
                            class="btn btn-primary"
                            wire:target="submit"
                            wire:loading.attr="disabled">
                            <x-spinner wire:loading wire:target="submit"/>
                            Save
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>
