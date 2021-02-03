@php
$items = [
    [
        'label' => 'Orders',
        'route' => 'backend.orders'
    ],
    [
        'label' => 'Customers',
        'route' => 'backend.customers'
    ],
    [
        'label' => 'Products',
        'route' => 'backend.products'
    ],
    [
        'label' => 'Data Import & Export',
        'route' => 'backend.import-export'
    ],
    [
        'label' => 'Users',
        'route' => 'backend.users'
    ],
    [
        'label' => 'Settings',
        'route' => 'backend.settings'
    ],
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.includes.head')
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ route('backend') }}">{{ config('app.name') }}</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        @foreach ($items as $item)
                            @php
                                $active = Str::of(Request::route()->getName())->startsWith($item['route']);
                            @endphp
                            <li class="nav-item @if($active) active @endif">
                                <a class="nav-link" href="{{ route($item['route']) }}">{{ $item['label'] }}@if($active)<span class="sr-only">(current)</span>@endif</a>
                            </li>
                        @endforeach
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @isset(Auth::user()->avatar)
                                    <img
                                        src="{{ Auth::user()->avatar }}"
                                        alt="Avatar"
                                        class="align-top bg-white rounded-circle"
                                        height="24"
                                        width="24">
                                @endisset
                                <span class="align-top ml-2">{{ Str::of(Auth::user()->name)->words(1, '') }}</span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('backend.user-profile') }}">User Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('backend.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" class="d-none" action="{{ route('backend.logout') }}" method="POST">
                                    {{ csrf_field() }}
                                </form>
                            </div>
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
