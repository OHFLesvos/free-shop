@inject('textRepo', 'App\Repository\TextBlockRepository')

<div>
    @if(session()->has('error'))
        <x-alert type="warning" dismissible>{{ session()->get('error') }}</x-alert>
    @endif
    @if($textRepo->exists('welcome'))
        <div class="mb-4">
            {!! $textRepo->getMarkdown('welcome') !!}
        </div>
    @endif
    @unless($shopDisabled)
        @if(isset($order))
            <x-alert type="success">
                {!! __('Your order has been submitted and your order number is <strong>#:id</strong>.', ['id' => $order->id]) !!}<br>
                {!! __('We will contact you via your phone <strong>:phone</strong> when the order is ready.', ['phone' => $order->customer->phone]) !!}
            </x-alert>
            @if(isset($nextOrderIn))
                <x-alert type="info">{{ __('You can place a new order in :time.', ['time' => $nextOrderIn->diffForHumans()]) }}</x-alert>
            @endif
            @isset($order->customer->nextTopUpDate)
                <x-alert type="info">{!! __('Next top-up on <strong>:date</strong>.', ['date' => $order->customer->nextTopUpDate->isoFormat('LL') ]) !!}</x-alert>
            @endif
            <p class="d-flex justify-content-between">
                <a href="{{ route('my-orders') }}" class="btn btn-primary">{{ __('View your orders') }}</a>
                <a href="{{ route('customer.logout') }}" class="btn btn-secondary">{{ __('Logout') }}</a>
            </p>
            @inject('textRepo', 'App\Repository\TextBlockRepository')
            @if($textRepo->exists('post-checkout'))
                {!! $textRepo->getMarkdown('post-checkout') !!}
            @endif
        @else
            @unless($dailyOrdersMaxedOut)
                @if($products->isNotEmpty())
                    <div class="row">
                        <div class="col-md-4 order-2">
                            @inject('geoBlockChecker', 'App\Services\GeoBlockChecker')
                            @if($geoBlockChecker->isBlocked())
                                <x-alert type="warning">
                                {{  __('The shop not available in your country. If you are using VPN, please disable it and reload this page.') }}
                                </x-alert>
                            @else
                            @unless(isset($nextOrderIn))
                                @include('livewire.shop-front.basket')
                            @endunless
                            @endif
                        </div>
                        <div class="col-md order-1">
                            @isset($nextOrderIn)
                                <x-alert type="info">{{ __('You can place a new order in :time.', ['time' => $nextOrderIn->diffForHumans()]) }}</x-alert>
                                <p><a href="{{ route('my-orders') }}" class="btn btn-primary">{{ __('View your orders') }}</a></p>
                            @endisset
                            @if($useCategories)
                                @foreach($categories as $category)
                                    @if($products->where('category', $category)->isNotEmpty())
                                        <h3 class="mb-3">{{ $category }}</h3>
                                        @include('livewire.shop-front.shop-products', ['products' => $products->where('category', $category)])
                                    @endif
                                @endforeach
                            @else
                                @include('livewire.shop-front.shop-products')
                            @endif
                        </div>
                    </div>
                @else
                    <x-alert type="info">{{ __('There are no products available at the moment.') }}</x-alert>
                @endif
            @else
                <x-alert type="info">{{ __('It is not possible to order something now because the maximum orders per day have been exceeded. Please visit us again another day.') }}</x-alert>
            @endunless
        @endif
    @else
        <x-alert type="info">
            <strong>{{ __('The shop is currently not available.') }}</strong>
            {!! $textRepo->getPlain('shop-disabled') !!}
        </x-alert>
    @endunless
</div>
