<div>
    <p>@lang('Please place an order from our selection of items:')</p>
    @if($products->isNotEmpty())
        <div class="row">
            <div class="col-md">
                @foreach($categories as $category)
                    <h4>{{ $category }}</h4>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3">
                        @foreach($products->where('category', $category) as $product)
                            <div class="col mb-4">
                                <div class="card shadow-sm">
                                    @isset($product->pictureUrl)
                                        <img
                                            src="{{ $product->pictureUrl }}"
                                            class="card-img-top"
                                            alt="Product name">
                                    @endisset
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text">{{ $product->description }}</p>
                                    </div>
                                    <div class="card-footer">
                                        <div class="input-group justify-content-end">
                                            @if($basket[$product->id] > 0)
                                                <div class="input-group-prepend">
                                                    <button
                                                        class="btn btn-primary"
                                                        wire:click="decrease({{ $product->id }})"
                                                        wire:loading.attr="disabled"
                                                        type="button"
                                                    >-</button>
                                                </div>
                                            @endif
                                            <input
                                                type="number"
                                                wire:model.lazy="basket.{{ $product->id }}"
                                                min="0"
                                                max="{{ $product->available_for_customer_amount }}"
                                                style="max-width: 7em"
                                                class="form-control text-center @error('basket.'.$product->id) is-invalid @enderror"
                                                placeholder="Amount">
                                            <div class="input-group-append">
                                                <button
                                                    class="btn @unless($basket[$product->id] < $product->available_for_customer_amount) btn-secondary @else btn-primary @endunless"
                                                    wire:click="increase({{ $product->id }})"
                                                    wire:loading.attr="disabled"
                                                    type="button"
                                                    @unless($basket[$product->id] < $product->available_for_customer_amount) disabled @endunless
                                                >+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm sticky mb-4">
                    <div class="card-header">@lang('Your order')</div>
                    @if($this->basketContents->isEmpty())
                        <div class="card-body">
                            @lang('Please add some products.')
                        </div>
                    @else
                        <table class="table m-0">
                            <tbody>
                                @foreach($this->basketContents as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td><strong>{{ $item['amount'] }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer text-right">
                            <button
                                class="btn @error('basket.*') btn-secondary @else btn-primary @enderror"
                                wire:click="checkout"
                                @error('basket.*') disabled @enderror
                                wire:loading.attr="disabled"
                                wire:target="checkout">
                                <x-bi-hourglass-split wire:loading wire:target="checkout"/>
                                @lang('Checkout')
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <x-alert type="info">No products available at the moment.</x-alert>
    @endif
</div>
