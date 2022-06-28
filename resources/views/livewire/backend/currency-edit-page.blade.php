<div x-data="{ shouldDelete: false }">
    <div class="small-container" x-show="shouldDelete" x-cloak>
        <x-card title="Delete currency">
            <p class="card-text">Really delete the currency <strong>{{ $currency->name }}</strong>?</p>
            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <span>
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:loading.attr="disabled"
                            @click="shouldDelete = false">
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            wire:target="delete"
                            wire:loading.attr="disabled"
                            wire:click="delete">
                            <x-spinner wire:loading wire:target="delete"/>
                            Delete
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </div>

    <div class="medium-container" x-show="!shouldDelete">
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <x-card :title="$title">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nameInput" class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control @error('currency.name') is-invalid @enderror"
                            id="nameInput"
                            required
                            @unless($currency->exists) autofocus @endunless
                            autocomplete="off"
                            wire:model.defer="currency.name">
                        @error('currency.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="top_up_amountInput" class="form-label">Top-up amount</label>
                        <input
                            type="number"
                            class="form-control @error('currency.top_up_amount') is-invalid @enderror"
                            id="top_up_amountInput"
                            required
                            min="0"
                            autocomplete="off"
                            wire:model.defer="currency.top_up_amount">
                        @error('currency.top_up_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="d-flex justify-content-between">
                        <span>
                            @if($currency->exists)
                                @can('delete', $currency)
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        wire:loading.attr="disabled"
                                        @click="shouldDelete = true">
                                        Delete
                                    </button>
                                @endcan
                            @endif
                        </span>
                        <span>
                            @can('viewAny', App\Models\Currency::class)
                                <a
                                    href="{{ route('backend.configuration.currencies') }}"
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
</div>
