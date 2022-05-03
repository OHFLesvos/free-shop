<div class="sticky">
@isset($customer)
    @php
        $balances = $this->getAvailableBalances();
    @endphp
@endif
@isset($customer)
    <x-card :title="__('Your account balance')">
        <p class="card-text">
            @forelse ($balances as $k => $v)
                <strong>{{ $v }}</strong> {{ $k }} <br>
            @empty
                <strong class="text-warning">{{ __("You currently don't have any balance available to spend.") }}</strong>
            @endforelse
        </p>
        @if($customer->nextTopUpDate !== null)
            <p class="card-text">
                {!! __('Next top-up on <strong>:date</strong>.', ['date' => $customer->nextTopUpDate->isoFormat('LL') ]) !!}
            </p>
        @endif
    </x-card>
@endisset
<x-card :title="__('Your order')">
    @isset($customer)
        @if($basket->isNotEmpty())
            <x-slot name="addon">
                <table class="table m-0">
                    <tbody>
                        @foreach($this->products->whereIn('id', $basket->items()->keys()) as $product)
                            <tr>
                                <td class="align-middle ps-3 fit text-end"><strong>{{ $basket->get($product->id) }}x</strong></td>
                                <td class="align-middle">{{ $product->name }}</td>
                                <td class="align-middle pe-3 fit">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        wire:click="add({{ $product->id }}, -1)"
                                        wire:loading.attr="disabled"
                                        aria-label="{{ __('Remove one') }}">
                                        <x-icon icon="minus"/>
                                    </button>
                                    @php
                                        $isAvailable = ($basket->get($product->id) ?? 0) < $product->getAvailableQuantityPerOrder();
                                        $canAfford = $product->price <= $this->getAvailableBalance($product->currency_id);
                                        $canAdd = $isAvailable && $canAfford;
                                    @endphp
                                    <button
                                        type="button"
                                        class="btn btn-sm @unless($canAdd) btn-secondary @else btn-success @endunless"
                                        wire:click="add({{ $product->id }}, 1)"
                                        wire:loading.attr="disabled"
                                        @unless($canAdd) disabled aria-disabled @endunless
                                        aria-label="{{ __('Add one') }}">
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
                        {{ __('Go to checkout') }}
                    </a>
                </div>
            </x-slot>
        @else
            @if($balances->sum() == 0)
                <p class="card-text">{{ __("You can't add any products right now.") }}</p>
            @else
                <p class="card-text">{{ __('Please add some products.') }}</p>
            @endif
        @endif
    @else
        <p class="card-text">{{ __('Please register or login to place an order.') }}</p>
        <a
            href="{{ route('customer.login') }}"
            class="btn btn-primary">
            {{ __('Login') }}
        </a>
    @endisset
</x-card>
</div>
