<div>
    <p>Welcome to our FREE shop. Please place an order from out selection of items:</p>
    @if($products->isNotEmpty())
        <div class="row">
            <div class="col-md">
                <div class="row row-cols-1 row-cols-md-2">
                    @foreach($products as $product)
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
                                    <p class="card-text"><small class="text-muted">{{ $product->category }}</small></p>
                                    <p class="card-text">{{ $product->description }}</p>
                                    @if($product->available_for_customer_amount > 0)
                                        <p class="card-text"><small class="text-muted">
                                            Available: {{ $product->available_for_customer_amount }}
                                        </small></p>
                                    @else
                                        <p class="card-text"><small class="text-danger">
                                            Not available
                                        </small></p>
                                    @endif
                                </div>
                                @if($product->available_for_customer_amount > 0)
                                    <div class="card-footer">
                                        <div class="input-group justify-content-end">
                                            <input
                                                type="number"
                                                wire:model="basket.{{ $product->id }}"
                                                min="0"
                                                max="{{ $product->available_for_customer_amount }}"
                                                style="max-width: 7em"
                                                class="form-control @error('basket.'.$product->id) is-invalid @enderror"
                                                placeholder="Amount">

                                                <div class="input-group-append">
                                                    <button
                                                        class="btn @unless($basket[$product->id] < $product->available_for_customer_amount) btn-secondary @else btn-primary @endunless"
                                                        wire:click="increase({{ $product->id }})"
                                                        type="button"
                                                        @unless($basket[$product->id] < $product->available_for_customer_amount) disabled @endunless
                                                    >+</button>
                                                </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm sticky mb-4">
                    <div class="card-header">Your order</div>
                    @if($this->basketContents->isEmpty())
                        <div class="card-body">
                            Please add some products.
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
                                Checkout
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
