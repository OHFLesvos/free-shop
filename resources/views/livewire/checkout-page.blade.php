<div class="small-container">
    @if(isset($order))
        <x-alert type="success">
            @lang('Your order has been submitted and your order number is <strong>#:id</strong>.', ['id' => $order->id])<br>
            @lang('We will contact you via your phone <strong>:phone</strong> when the order is ready.', ['phone' => $order->customer->phone])
        </x-alert>
        <p><a href="{{ route('my-orders') }}" class="btn btn-primary">@lang('View your orders')</a></p>
        @inject('textRepo', 'App\Repository\TextBlockRepository')
        @if($textRepo->exists('post-checkout'))
            {!! $textRepo->getMarkdown('post-checkout') !!}
        @endif
    @elseif(isset($nextOrderIn))
        <x-alert type="info">@lang('You can place a new order in :time.', ['time' => $nextOrderIn])</x-alert>
        <p><a href="{{ route('my-orders') }}" class="btn btn-primary">@lang('View your orders')</a></p>
    @elseif ($basket->isNotEmpty())
        <form wire:submit.prevent="submit" autocomplete="off">
            @php
                $total = 0;
            @endphp
            <x-card :title="__('Your Order')">
                <p class="mb-1">{{ __('Selected products:') }}</p>
                <table class="table">
                    <tbody>
                        @foreach(App\Models\Product::whereIn('id', $basket->keys())->get()->sortBy('name') as $product)
                            <tr>
                                <td class="fit text-end align-middle">
                                    <strong>{{ $basket[$product->id] }}x</strong>
                                </td>
                                <td class="align-middle">
                                    {{ $product->name }}
                                    <small class="text-muted ms-1">{{ $product->category }}</small>
                                </td>
                                <td class="align-middle text-end fit">
                                    @php
                                        $price = $basket[$product->id] * $product->price;
                                        $total += $price;
                                    @endphp
                                    {{ $price }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end">
                                <strong>@lang('Total')</strong>
                            </td>
                            <td class="text-end fit">
                                <u><strong>{{ $total }}</strong></u>
                            </td>
                        </tr>
                    </tfoot>
                </table>
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
                <p class="card-text d-flex align-items-center">
                    <x-icon icon="info-circle" class="fa-2x me-3"/>
                    <span>
                        @lang('We will send you updates about your order via SMS to <strong>:phone</strong>.', ['phone' => $customer->phoneFormattedInternational])
                        @lang('You can update your phone number <a href=":url">here</a>.', ['url' => route('customer.account')])</a>
                    </span>
                </p>
                <x-slot name="footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('shop-front') }}" class="btn btn-secondary">
                            @lang('Change')
                        </a>
                        @if($total <= $customer->credit)
                            <button
                                type="submit"
                                class="btn btn-primary">
                                <x-spinner wire:loading wire:target="submit"/>
                                @lang('Send order')
                            </button>
                        @else
                            <span class="text-danger">
                                @lang('Not enough credit.')
                            </span>
                        @endif
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
