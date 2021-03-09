<div>
    @unless($shopDisabled)
        @unless($maxOrdersReached)
            @if($basket->isNotEmpty())
                <p class="d-md-none">
                    <a href="{{ route('checkout') }}"
                        class="btn btn-primary btn-block">
                        @lang('Go to checkout')
                    </a>
                </p>
            @endif
            @isset($nextOrderIn)
                <x-alert type="info">@lang('You can place a new order in :time.', ['time' => $nextOrderIn])</x-alert>
            @endisset
            @if($products->isNotEmpty())
                <div class="row">
                    <div class="col-md">
                        @if($products->isNotEmpty())
                            <p>@lang('Please place an order from our selection of items:')</p>
                        @endif
                        {{-- @foreach($categories as $category)
                            @if($products->where('category', $category)->isNotEmpty())
                                <h3 class="mb-3">{{ $category }}</h3> --}}
                                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xxl-4">
                                    @foreach($products as $product)
                                        <div class="col mb-4">
                                            <div class="card h-100 shadow-sm">
                                                @isset($product->pictureUrl)
                                                    <img
                                                        src="{{ url($product->pictureUrl) }}"
                                                        class="card-img-top"
                                                        alt="Product name">
                                                @endisset
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        {{ $product->name }}
                                                    </h5>
                                                    <p class="card-text mb-2"><span class="badge bg-secondary">{{ $product->category }}</span></p>
                                                    <p class="card-text">{{ $product->description }}</p>
                                                </div>
                                                <div class="card-footer d-flex justify-content-between align-items-center">
                                                    <strong>
                                                        @if($product->price > 0)
                                                            @lang('Price: :amount', ['amount' => $product->price])
                                                        @else
                                                            Free
                                                        @endif
                                                    </strong>
                                                    @unless(isset($nextOrderIn))
                                                        @isset($customer)
                                                            <button
                                                                class="btn @unless(($basket[$product->id] ?? 0) < $product->quantity_available_for_customer) btn-secondary @else btn-primary @endunless"
                                                                wire:click="add({{ $product->id }})"
                                                                wire:loading.attr="disabled"
                                                                wire:target="add"
                                                                @unless(($basket[$product->id] ?? 0) < $product->quantity_available_for_customer) disabled @endunless>
                                                                @unless(($basket[$product->id] ?? 0) < $product->quantity_available_for_customer)
                                                                    @lang('Maximum')
                                                                @else
                                                                    @lang('Add')
                                                                @endunless
                                                            </button>
                                                        @else
                                                            <a
                                                                href="{{ route('customer.login') }}"
                                                                class="btn btn-primary">
                                                                @lang('Add')
                                                            </a>
                                                        @endisset
                                                    @endunless
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            {{-- @endif
                        @endforeach --}}
                    </div>
                    <div class="col-md-4">
                        @unless(isset($nextOrderIn))
                            <x-card :title="__('Your order')" class="sticky">
                                @isset($customer)
                                    @if($basket->isNotEmpty())
                                        <x-slot name="addon">
                                            <table class="table m-0">
                                                <tbody>
                                                    @foreach($this->products->whereIn('id', $basket->keys()) as $product)
                                                        <tr>
                                                            <td class="align-middle ps-3">{{ $product->name }}</td>
                                                            <td class="align-middle fit text-end"><strong>{{ $basket[$product->id] }}</strong></td>
                                                            <td class="align-middle pe-3 fit">
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
                                        </x-slot>
                                        <x-slot name="footer">
                                            <div class="d-flex justify-content-end">
                                                <a href="{{ route('checkout') }}"
                                                    class="btn btn-primary">
                                                    @lang('Go to checkout')
                                                </a>
                                            </div>
                                        </x-slot>
                                    @else
                                        <p class="card-text">@lang('Please add some products.')</p>
                                    @endif
                                @else
                                    <p class="card-text">@lang('Please register or login to place an order.')</p>
                                    <a
                                        href="{{ route('customer.login') }}"
                                        class="btn btn-primary">
                                        @lang('Login')
                                    </a>
                                @endisset
                            </x-card>
                        @endunless
                    </div>
                </div>
            @else
                <x-alert type="info">@lang('There are no products available at the moment.')</x-alert>
            @endif
        @else
            <x-alert type="info">@lang('It is not possible to order something now because the maximum orders per day have been exceeded. Please visit us again another day.')</x-alert>
        @endunless
    @else
        <x-alert type="info">@lang('The shop is currently not available.')</x-alert>
    @endunless
</div>
