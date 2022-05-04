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
            <x-slot name="addon">
                <ul class="list-group list-group-flush">
                    @foreach ($products->groupBy('category') as $k => $v)
                        <li class="list-group-item">
                            <h6 class="my-2">{{ $k }}</h6>
                        </li>
                        @foreach ($v as $product)
                            <li class="list-group-item">
                                <div class="row align-items-center g-2">
                                    <div class="col-sm">
                                        <label for="product{{ $product->id }}Input">
                                            {{ $product->name }}
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        @if($product->price > 0 && $product->currency_id !== null)
                                            <strong>{{ $product->price }}</strong> {{ $product->currency->name }}
                                        @else
                                            <strong class="text-success">Free</strong>
                                        @endif
                                    </div>
                                    <div class="col-sm-2">
                                        <small>
                                            Available: {{ $product->freeQuantity }}<br>
                                            Limit: {{ $product->getAvailableQuantityPerOrder() }}
                                        </small>
                                    </div>
                                    <div class="col-sm-2">
                                        <input
                                            type="number"
                                            min="0"
                                            max="{{ $product->getAvailableQuantityPerOrder() }}"
                                            wire:model="selection.{{ $product->id }}"
                                            placeholder="0"
                                            class="form-control"
                                            id="product{{ $product->id }}Input">
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endforeach
                    <li class="list-group-item text-end">
                        <strong>Total costs:</strong>
                        {{ $this->totalPrice }}
                        <br>
                        <strong>Customer balance:</strong>
                        {{ $customer->totalBalance() }}
                    </li>
                </ul>
                <div class="card-body">
                    <div>
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
            </x-slot>
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
