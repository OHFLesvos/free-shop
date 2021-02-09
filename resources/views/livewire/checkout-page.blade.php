<div class="small-container">
    @if(isset($order))
        <x-alert type="success">
            @lang('Your order has been submitted and your order number is <strong>#:id</strong>.', ['id' => $order->id])<br>
            @lang('We will contact you via your phone <strong>:phone</strong> when the order is ready.', ['phone' => $order->customer->phone])
        </x-alert>
    @elseif ($basket->isNotEmpty())
        <form wire:submit.prevent="submit" autocomplete="off">
            <x-card :title="__('Your Order')">
                <p class="mb-1">{{ __('Selected products:') }}</p>
                <table class="table">
                    <tbody>
                        @foreach(App\Models\Product::whereIn('id', $basket->keys())->get()->sortBy('name') as $product)
                            <tr>
                                <td class="fit text-end align-middle ps-3">
                                    <strong>{{ $basket[$product->id] }}</strong>
                                </td>
                                <td class="align-middle">
                                    {{ $product->name }}
                                    <small class="text-muted ms-1">{{ $product->category }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p>
                    <a href="{{ route('shop-front') }}" class="btn btn-secondary btn-sm">
                        @lang('Change')
                    </a>
                </p>
                <div class="mb-3">
                    <label for="inputRemarks" class="form-label">@lang('Remarks')</label>
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
                <p class="card-text">@lang('We will send you updates about your order via SMS to <strong>:phone</strong>.', ['phone' => $customer->phoneFormattedInternational])</p>
                <x-slot name="footer">
                    <div class="text-end">
                        <button
                            type="submit"
                            class="btn btn-primary">
                            <x-spinner wire:loading wire:target="submit"/>
                            @lang('Send order')
                        </button>
                    </div>
                </x-slot>
            </x-card>
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
