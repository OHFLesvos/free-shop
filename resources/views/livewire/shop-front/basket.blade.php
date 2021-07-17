<x-card :title="__('Your order')" class="sticky">
    @isset($customer)
        <p class="card-text">
            <strong>
                {{  __(':amount points available', ['amount' => $this->availableCredit]) }}
            </strong>
        </p>
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
                                    <button
                                        type="button"
                                        class="btn btn-sm @unless($basket->get($product->id) < $product->quantity_available_for_customer && $product->price <= $this->availableCredit) btn-secondary @else btn-success @endunless"
                                        wire:click="add({{ $product->id }}, 1)"
                                        wire:loading.attr="disabled"
                                        @unless($basket->get($product->id) < $product->quantity_available_for_customer && $product->price <= $this->availableCredit) disabled aria-disabled @endunless
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
            @if($this->availableCredit == 0)
                <p class="card-text">
                    {{ __("You currently don't have any points available to spend.") }}
                    @isset($customer->nextTopUpDate)
                        {!! __('Next top-up on <strong>:date</strong>.', ['date' => $customer->nextTopUpDate->isoFormat('LL') ]) !!}
                    @endif
                </p>
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