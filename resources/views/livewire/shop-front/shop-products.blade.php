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
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <strong>
                        @if($product->price > 0)
                            @lang('Price: :amount', ['amount' => $product->price])
                        @else
                            @lang('Free')
                        @endif
                    </strong>
                    @unless(isset($nextOrderIn))
                        @isset($customer)
                            <button
                                class="btn @unless(($basket[$product->id] ?? 0) < $product->quantity_available_for_customer && $product->price <= $this->availableCredit) btn-secondary @else btn-primary @endunless"
                                wire:click="add({{ $product->id }})"
                                wire:loading.attr="disabled"
                                wire:target="add"
                                @unless(($basket[$product->id] ?? 0) < $product->quantity_available_for_customer && $product->price <= $this->availableCredit) disabled @endunless>
                                @lang('Add')
                            </button>
                        @else
                            <a
                                href="{{ route('customer.login') }}"
                                class="btn btn-primary">
                                @lang('Get')
                            </a>
                        @endisset
                    @endunless
                </div>
            </div>
        </div>
    @endforeach
</div>