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
                                    <div class="card-footer text-right">
                                        <button
                                            class="btn @unless(($basket[$product->id] ?? 0) < $product->quantity_available_for_customer) btn-secondary @else btn-primary @endunless"
                                            wire:click="add({{ $product->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="add"
                                            @unless(($basket[$product->id] ?? 0) < $product->quantity_available_for_customer) disabled @endunless
                                            >
                                            @lang('Add')
                                        </button>
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
                    @if($basket->isNotEmpty())
                        <table class="table m-0">
                            <tbody>
                                @foreach($this->products->whereIn('id', $basket->keys()) as $product)
                                    <tr>
                                        <td class="align-middle">{{ $product->name }}</td>
                                        <td class="align-middle"><strong>{{ $basket[$product->id] }}</strong></td>
                                        <td class="align-middle fit">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger"
                                                wire:click="add({{ $product->id }}, -1)"
                                                wire:loading.attr="disabled"
                                                aria-label="@land('Remove one')">
                                                <x-icon icon="minus"/>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-sm @unless($basket[$product->id] < $product->quantity_available_for_customer) btn-secondary @else btn-success @endunless"
                                                wire:click="add({{ $product->id }}, 1)"
                                                wire:loading.attr="disabled"
                                                @unless($basket[$product->id] < $product->quantity_available_for_customer) disabled aria-disabled @endunless
                                                aria-label="@lang('Add one')">
                                                <x-icon icon="plus"/>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer text-right">
                            <a href="{{ route('checkout') }}"
                                class="btn btn-primary">
                                @lang('Checkout')
                            </a>
                        </div>
                    @else
                        <div class="card-body">
                            @lang('Please add some products.')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <x-alert type="info">@lang('There are no products available at the moment.')</x-alert>
    @endif
</div>
