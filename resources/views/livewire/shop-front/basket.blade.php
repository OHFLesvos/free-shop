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