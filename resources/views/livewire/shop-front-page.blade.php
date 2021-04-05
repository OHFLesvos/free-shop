@inject('textRepo', 'App\Repository\TextBlockRepository')

<div>
    @if($textRepo->exists('welcome'))
        <div class="mb-4">
            {!! $textRepo->getMarkdown('welcome') !!}
        </div>
    @endif
    @unless($shopDisabled)
        @unless($maxOrdersReached)
            @isset($nextOrderIn)
                <x-alert type="info">@lang('You can place a new order in :time.', ['time' => $nextOrderIn])</x-alert>
            @endisset
            @if($products->isNotEmpty())
                <div class="row">
                    <div class="col-md-4 order-md-2">
                        @unless(isset($nextOrderIn))
                            @include('livewire.shop-front.basket')
                        @endunless
                    </div>
                    <div class="col-md order-md-1">
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
                @if($basket->isNotEmpty())
                    <p class="d-md-none">
                        <a href="{{ route('checkout') }}"
                            class="btn btn-primary btn-block">
                            @lang('Go to checkout')
                        </a>
                    </p>
                @endif
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
