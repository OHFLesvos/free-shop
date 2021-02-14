@php
$items = [
    [
        'label' => 'Dashboard',
        'route' => 'backend.dashboard',
    ],
    [
        'label' => 'Orders',
        'route' => 'backend.orders',
        'authorized' => auth()->user()->canAny(['view orders', 'update orders']),
    ],
    [
        'label' => 'Customers',
        'route' => 'backend.customers',
        'authorized' => auth()->user()->canAny(['view customers', 'manage customers']),
    ],
    [
        'label' => 'Products',
        'route' => 'backend.products'
    ],
    [
        'label' => 'Data Import & Export',
        'route' => 'backend.import-export',
        'authorized' => auth()->user()->canAny(['export data', 'import data']),
    ],
    [
        'label' => 'Users',
        'route' => 'backend.users',
        'authorized' => auth()->user()->canAny(['manage users']),
    ],
    [
        'label' => 'Settings',
        'route' => 'backend.settings',
        'authorized' => auth()->user()->can('update settings'),
    ],
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.includes.head')
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
            <div class="container">
                <span class="d-lg-none text-light">{{ $title }}</span>
                <a class="navbar-brand d-none d-lg-inline" href="{{ route('backend') }}">{{ config('app.name') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @foreach (collect($items)->filter(fn ($item) => !isset($item['authorized']) || $item['authorized']) as $item)
                            @php
                                $active = Str::of(Request::route()->getName())->startsWith($item['route']);
                            @endphp
                            <li class="nav-item">
                                <a
                                    class="nav-link @if($active) active @endif"
                                    href="{{ route($item['route']) }}"
                                    @if($active) aria-current="page" @endif>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            @php
                                $active = Str::of(Request::route()->getName())->startsWith('backend.user-profile');
                            @endphp
                            <a
                                class="nav-link @if($active) active @endif dropdown-toggle"
                                href="#"
                                id="navbarDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                @isset(Auth::user()->avatar)
                                    <img
                                        src="{{ Auth::user()->avatar }}"
                                        alt="Avatar"
                                        class="align-top bg-white rounded-circle"
                                        height="24"
                                        width="24">
                                @endisset
                                <span class="align-top ms-2">{{ Str::of(Auth::user()->name)->words(1, '') }}</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('backend.user-profile') }}">User Profile</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backend.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" class="d-none" action="{{ route('backend.logout') }}" method="POST">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}" target="_blank">
                                Frontend
                                <x-icon icon="external-link-alt"/>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <main>
            <div class="container">
                {{ $slot }}
            </div>
        </main>
        @include('layouts.includes.foot')
    </body>
</html>
