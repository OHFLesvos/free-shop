<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@isset($title){{ $title }} | @endisset{{ config('app.name') }}</title>
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        @livewireStyles
        @stack('styles')
    </head>
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ route('backend') }}">{{ config('app.name') }}</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        @php
                            $items = [
                                [
                                    'label' => 'Orders',
                                    'route' => 'backend.orders'
                                ],
                                [
                                    'label' => 'Products',
                                    'route' => 'backend.products'
                                ],
                                [
                                    'label' => 'Data Export',
                                    'route' => 'backend.export'
                                ],
                                [
                                    'label' => 'Settings',
                                    'route' => 'backend.settings'
                                ],
                            ];
                        @endphp
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
                        <li class="nav-item">
                            <a
                                class="nav-link @isset(Auth::user()->avatar) py-1 @endif @if(Request::route()->getName() == 'backend.user-profile') active @endif"
                                href="{{ route('backend.user-profile') }}"
                                aria-label="User profile">
                                @isset(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="bg-white rounded-circle" height="32">
                                @else
                                    {{ Auth::user()->name }}
                                @endisset
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}" target="_blank">
                                Frontend
                                <x-icon icon="external-link-alt"/>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" class="d-none" action="{{ route('logout') }}" method="POST">
                                {{ csrf_field() }}
                            </form>
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
        <script src="{{ mix('js/manifest.js') }}" defer></script>
        <script src="{{ mix('js/vendor.js') }}" defer></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
        @livewireScripts
        @stack('scripts')
    </body>
</html>
