@inject('geoBlockChecker', 'App\Services\GeoBlockChecker')
@php
$navItems = [
    [
        'label' => __('Shop'),
        'route' => 'shop-front',
        'icon' => 'shopping-bag',
        'authorized' => true,
    ],
    [
        'label' => __('Checkout'),
        'route' => 'checkout',
        'icon' => 'shopping-basket',
        'authorized' => auth('customer')->check(),
    ],
    [
        'label' => __('Your orders'),
        'route' => 'my-orders',
        'icon' => 'list-alt',
        'authorized' => auth('customer')->check(),
    ],
    [
        'label' => __('About'),
        'route' => 'about',
        'icon' => 'info-circle',
        'authorized' => true,
    ],
    [
        'label' => __('Statistics'),
        'route' => 'statistics',
        'icon' => 'chart-bar',
        'authorized' => true,
    ],
];
$rNavItems = [
    [
        'label' => __('Login'),
        'route' => 'customer.login',
        'icon' => 'sign-in-alt',
        'authorized' => !$geoBlockChecker->isBlocked() && !auth('customer')->check(),
    ],
    [
        'label' => optional(auth('customer')->user())->name,
        'route' => 'customer.account',
        'icon' => 'id-card',
        'authorized' => auth('customer')->check(),
    ],
    [
        'label' => __('Logout'),
        'route' => 'customer.logout',
        'icon' => 'sign-out-alt',
        'authorized' => auth('customer')->check(),
    ],
];
$rtl = collect(config('localization.languages'))->contains(fn ($language) => app()->getLocale() == $language['code'] && $language['rtl'] === true);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if($rtl) dir="rtl" @endif>
    @include('layouts.includes.head')
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    @if(setting()->has('brand.logo') && Storage::exists(setting()->get('brand.logo')))
                        <img src="{{ storage_url(setting()->get('brand.logo')) }}" alt="Logo" height="24" class="me-1" />
                    @endif
                    {{ config('app.name') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @foreach (collect($navItems)->filter(fn($item) => !isset($item['authorized']) || $item['authorized']) as $item)
                            <x-nav-item :item="$item"/>
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
                                {{ __('Switch language') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                @foreach(config('localization.languages') as $language)
                                    <li>
                                        <a
                                            class="dropdown-item"
                                            href="{{ route('languages.update', $language['code']) }}">
                                            @if(session()->get('lang') == $language['code'])
                                                <strong>{{ $language['name_localized'] }}</strong>
                                            @else
                                                {{ $language['name_localized'] }}
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        @foreach (collect($rNavItems)->filter(fn($item) => !isset($item['authorized']) || $item['authorized']) as $item)
                            <x-nav-item :item="$item"/>
                        @endforeach
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
                    @include('layouts.includes.copyright')
                    | <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
                </small>
            </p>
        </footer>
        @include('layouts.includes.foot')
    </body>
</html>
