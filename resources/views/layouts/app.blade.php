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
@endphp
@inject('localization', 'App\Services\LocalizationService')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if($localization->isRtlLocale()) dir="rtl" @endif class="h-100">
    @include('layouts.includes.head')
    <body class="d-flex flex-column h-100 bg-light">
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
                                @foreach($localization->getLocalizedNames(true) as $key => $value)
                                    <li>
                                        <a
                                            class="dropdown-item"
                                            href="{{ route('languages.update', $key) }}">
                                            @if(session()->get('lang') == $key)
                                                <strong>{{ $value }}</strong>
                                            @else
                                                {{ $value }}
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
        <main class="flex-shrink-0">
            <div class="container">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </main>
        <footer class="footer mt-auto py-4 bg-white border-top shadow-sm">
            <div class="container">
                <div class="d-lg-flex justify-content-between align-items-end">
                    <div class="d-flex align-items-center">
                    <img src="{{asset('img/signet_ohf.png')}}" style="height: 50px" class="me-3"/>
                    <div>
                        <small>
                            <strong>{{ config('app.name') }}</strong><br>
                            @include('layouts.includes.copyright')
                        </small>
                    </div>
                </div>
                    <div class="text-end mt-4 mt-lg-0">
                        <small>
                            <a href="{{ route('about') }}">{{ __('About') }}</a>
                            <a href="{{ route('privacy-policy') }}" class="ms-2">{{ __('Privacy Policy') }}</a>
                        </small>
                    </div>
                </div>
            </div>
        </footer>
        @include('layouts.includes.foot')
    </body>
</html>
