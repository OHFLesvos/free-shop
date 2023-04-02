@inject('textRepo', 'App\Repository\TextBlockRepository')

<div>
    @if (session()->has('error'))
        <x-alert type="warning" dismissible>{{ session()->get('error') }}</x-alert>
    @endif
    @if ($textRepo->exists('welcome'))
        <div class="mb-4">
            {!! $textRepo->getMarkdown('welcome') !!}
        </div>
    @endif
    @unless($shopDisabled)
        @unless($dailyOrdersMaxedOut)
            @if ($products->isNotEmpty())
                <div class="row">
                    <div class="col-md-4 order-md-2">
                        @inject('geoBlockChecker', 'App\Services\GeoBlockChecker')
                        @if ($geoBlockChecker->isBlocked())
                            <x-alert type="warning">
                                {{ __('The shop not available in your country. If you are using VPN, please disable it and reload this page.') }}
                            </x-alert>
                        @else
                            @unless(isset($nextOrderIn))
                                @include('livewire.shop-front.basket')
                            @endunless
                        @endif
                    </div>
                    <div class="col-md order-md-1">
                        @isset($nextOrderIn)
                            <x-alert type="info">
                                {{ __('You can place a new order on :date.', ['date' => $nextOrderIn->isoFormat('LL')]) }}</x-alert>
                        @endisset
                        @if ($useCategories)
                            @foreach ($categories as $category)
                                @if ($products->where('category', $category)->isNotEmpty())
                                    <h3 class="mb-3">{{ $category }}</h3>
                                    @include('livewire.shop-front.shop-products', [
                                        'products' => $products->where('category', $category),
                                    ])
                                @endif
                            @endforeach
                        @else
                            @include('livewire.shop-front.shop-products')
                        @endif
                    </div>
                </div>
                @if ($basket->isNotEmpty())
                    <p class="d-md-none">
                        <a href="{{ route('checkout') }}" class="btn btn-primary btn-block">
                            {{ __('Go to checkout') }}
                        </a>
                    </p>
                @endif
            @else
                <x-alert type="info">{{ __('There are no products available at the moment.') }}</x-alert>
            @endif
        @else
            <x-alert type="info">
                {{ __('It is not possible to order something now because the maximum orders per day have been exceeded. Please visit us again another day.') }}
            </x-alert>
        @endunless
    @else
        <x-alert type="info">
            <strong>{{ __('The shop is currently not available.') }}</strong>
            {!! $textRepo->getPlain('shop-disabled') !!}
        </x-alert>
    @endunless
</div>
