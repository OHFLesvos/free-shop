@php
$readyOrders = 0;
if (CurrentCustomer::exists()) {
    $readyOrders = CurrentCustomer::get()->orders()->status('ready')->count();
}
$items = [
    [
        'label' => __('Shop'),
        'route' => 'shop-front',
        'icon' => 'shopping-bag',
    ],
    [
        'label' => __('Your orders'),
        'route' => 'my-orders',
        'icon' => 'list-alt',
        'badge' => $readyOrders > 0 ? $readyOrders : null,
    ],
    [
        'label' => __('About'),
        'route' => 'about',
        'icon' => 'info-circle',
    ],    
];
$rtl = in_array(app()->getLocale(), config('app.rtl_languages', []));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if($rtl) dir="rtl" @endif>
    @include('layouts.includes.head')
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    @if(setting()->has('brand.logo') && Storage::exists(setting()->get('brand.logo')))
                        <img src="{{ url(Storage::url(setting()->get('brand.logo'))) }}" alt="Logo" height="24" class="me-1" />
                    @endif
                    {{ config('app.name') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @foreach ($items as $item)
                            @php
                                $active = Str::of(Request::route()->getName())->startsWith($item['route']);
                            @endphp
                            <li class="nav-item">
                                <a
                                    class="nav-link @if($active) active @endif"
                                    href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif>
                                    @isset($item['icon']) <x-icon :icon="$item['icon']"/> @endisset
                                    {{ $item['label'] }}
                                    @isset($item['badge'])
                                        <span class="badge bg-info">{{ $item['badge'] }}</span>
                                    @endisset
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <ul class="navbar-nav">
                        {{-- Language chooser --}}
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#" id="navbarDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <x-icon icon="language"/>
                                @lang('Switch language')
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                @foreach(config('app.supported_languages') as $key => $val)
                                    <li>
                                        <a
                                            class="dropdown-item"
                                            href="{{ route('languages.change', $key) }}">
                                            @if(session()->get('lang') == $key)
                                                <strong>{{ $val }}</strong>
                                            @else
                                                {{ $val }}
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        {{-- Customer --}}
                        @if(CurrentCustomer::exists())
                            @php
                                $active = Str::of(Request::route()->getName())->startsWith('customer.account');
                            @endphp
                            <li class="nav-item">
                                <a
                                    class="nav-link @if($active) active @endif"
                                    href="{{ route('customer.account') }}"
                                    @if($active) aria-current="page" @endif>
                                    <x-icon icon="id-card"/>
                                    {{ CurrentCustomer::get()->name }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <main>
            <div class="container">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </main>
        <footer class="mt-5">
            <p class="text-center">
                <small>
                    &copy;{{ now()->format('Y') }}
                    <a href="https://ohf-lesvos.org" target="_blank">OHF Lesvos</a>
                    | <a href="{{ route('privacy-policy') }}">@lang('Privacy Policy')</a>
                </small>
            </p>
        </footer>
        @include('layouts.includes.foot')
    </body>
</html>
