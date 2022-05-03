@inject('geoBlockChecker', 'App\Services\GeoBlockChecker')
@php
    $geoblocked = $geoBlockChecker->isBlocked();
@endphp
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
                    @unless($useCategories)
                        <p class="card-text">
                            <span class="badge bg-secondary">{{ $product->category }}</span>
                        </p>
                    @endunless
                    @if(filled($product->description))
                        <p class="card-text">{{ $product->description }}</p>
                    @endif
                    @isset($product->limit_per_order)
                        @php
                            $label = __('Maximum :quantity per order.', ['quantity' => $product->limit_per_order]);
                        @endphp
                        <p class="card-text">
                            @if(($basket->get($product->id) ?? 0) == $product->limit_per_order)
                                <strong class="text-warning">{{ $label }}</strong>
                            @else
                                <strong class="text-info">{{ $label }}</strong>
                            @endif
                        </p>
                    @endisset
                </div>
                <div class="card-footer">
                    @if($product->price > 0 && $product->currency_id !== null)
                        <strong>{{ $product->price }}</strong> {{ $product->currency->name }}
                    @else
                        <strong class="text-success">{{ __('Free') }}</strong>
                    @endif
                </div>
                <div class="card-footer">
                    @unless($geoblocked)
                        @unless(isset($nextOrderIn))
                            @isset($customer)
                                @php
                                    $isAvailable = ($basket->get($product->id) ?? 0) < $product->getAvailableQuantityPerOrder();
                                    $canAfford = $product->price <= $this->getAvailableBalance($product->currency_id);
                                    $canAdd = $isAvailable && $canAfford;
                                @endphp
                                @if($basket->get($product->id) ?? 0 > 0)
                                    <div class="row align-items-center">
                                        <div class="col d-grid">
                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                wire:click="add({{ $product->id }}, -1)"
                                                wire:loading.attr="disabled"
                                                aria-label="{{ __('Remove one') }}">
                                                <x-icon icon="minus" fixed-width/>
                                            </button>
                                        </div>
                                        <div class="col-auto text-center px-0">
                                            <big><strong>{{ $basket->get($product->id) ?? 0 }}</strong></big>
                                        </div>
                                        <div class="col d-grid text-end">
                                            <button
                                                type="button"
                                                class="btn @unless($canAdd) btn-secondary @else btn-success @endunless"
                                                wire:click="add({{ $product->id }}, 1)"
                                                wire:loading.attr="disabled"
                                                @unless($canAdd) disabled aria-disabled @endunless
                                                aria-label="{{ __('Add one') }}">
                                                <x-icon icon="plus" fixed-width/>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-grid">
                                        <button
                                            class="btn @unless($canAdd) btn-secondary @else btn-primary @endunless"
                                            wire:click="add({{ $product->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="add"
                                            @unless($canAdd) disabled @endunless>
                                            {{ __('Add') }}
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="d-grid">
                                    <a
                                        href="{{ route('customer.login') }}"
                                        class="btn btn-primary">
                                        {{ __('Get') }}
                                    </a>
                                </div>
                            @endisset
                        @endunless
                    @else
                    <button
                        class="btn btn-secondary"
                        disabled>
                        {{ __('Get') }}
                    </button>
                    @endunless
                </div>
            </div>
        </div>
    @endforeach
</div>
