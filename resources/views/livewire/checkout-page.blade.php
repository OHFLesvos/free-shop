<div class="medium-container">
    @if(isset($order))
        <x-alert type="success">
            @lang('Your order has been submitted and your order number is <strong>#:id</strong>.', ['id' => $order->id])<br>
            @lang('We will contact you via your phone <strong>:phone</strong> when the order is ready.', ['phone' => $order->customer->phone])
        </x-alert>
    @elseif ($basket->isNotEmpty())
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    @lang('Selected products')
                    <a href="{{ route('shop-front') }}" class="btn btn-primary btn-sm">
                        @lang('Change')
                    </a>
                </div>
                <table class="table m-0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell"></th>
                            <th>@lang('Product')</th>
                            <th class="text-end">@lang('Quantity')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(App\Models\Product::whereIn('id', $basket->keys())->get()->sortBy('name') as $product)
                            <tr>
                                <td class="fit d-none d-md-table-cell">
                                    @isset($product->pictureUrl)
                                        <img
                                            src="{{ $product->pictureUrl }}"
                                            alt="Product Image"
                                            style="max-width: 100px; max-height: 75px"/>
                                    @endisset
                                </td>
                                <td class="align-middle">
                                    {{ $product->name }}
                                    <small class="text-muted ms-1">{{ $product->category }}</small>
                                </td>
                                <td class="text-end align-middle">
                                    <strong>{{ $basket[$product->id] }}</strong>
                                </td>
                            </tr>
                            {{-- <div class="input-group justify-content-end">
                            <input
                                type="number"
                                wire:model.lazy="basket.{{ $product->id }}"
                                value="{{ $basket[$product->id] ?? 0 }}"
                                min="0"
                                max="{{ $product->quantity_available_for_customer }}"
                                style="max-width: 7em"
                                class="form-control text-center @error('basket.'.$product->id) is-invalid @enderror"
                                placeholder="Quantity">
                        </div> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">@lang('Your Order')</div>
                <div class="card-body">
                    <p>@lang('We will send you updates about your order via SMS to <strong>:phone</strong>.', ['phone' => $customer->phoneFormattedInternational])</p>
                    <div class="mb-3">
                        <label for="inputRemarks">@lang('Remarks')</label>
                        <textarea
                            class="form-control @error('remarks') is-invalid @enderror"
                            id="inputRemarks"
                            wire:model.defer="remarks"
                            rows="3"
                            autocomplete="off"
                            aria-describedby="remarksHelp"></textarea>
                        @error('remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="remarksHelp" class="form-text text-muted">
                            @lang('Please write if we need to know anything more regarding your order.')
                        </small>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    wire:click="restart">
                    @lang('Restart')
                </button>
                <button
                    type="submit"
                    class="btn btn-primary">
                    {{-- class="btn @error('basket.*') btn-secondary @else btn-primary @enderror" --}}
                    {{-- @error('basket.*') disabled @enderror --}}
                    <x-spinner wire:loading wire:target="submit"/>
                    @lang('Send order')
                </button>
            </div>
        </form>
    @else
        <x-alert type="warning">
            @lang('No products selected.')
        </x-alert>
        <p>
            <a href="{{ route('shop-front') }}" class="btn btn-primary">@lang('Choose products')</a>
        </p>
    @endif
</div>
