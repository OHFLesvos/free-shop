@php
$items = [
    [
        'label' => __('Shop'),
        'route' => 'shop-front',
    ],
    [
        'label' => __('Your orders'),
        'route' => 'order-lookup',
    ],
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.includes.head')
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container container-narrow">
                <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
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
                        {{-- Customer --}}
                        @if(CurrentCustomer::exists())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.account') }}">{{ CurrentCustomer::get()->name }}</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.login') }}">@lang('Customer Login')</a>
                            </li>
                        @endif
                        {{-- Language chooser --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @lang('Switch language')
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                @foreach(config('app.supported_languages') as $key => $val)
                                    <a class="dropdown-item" href="{{ route('languages.change', $key) }}">@if(session()->get('lang') == $key)<strong>{{ $val }}</strong>@else{{ $val }}@endif</a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <main>
            <div class="container container-narrow">
                {{ $slot }}
            </div>
        </main>
        @include('layouts.includes.foot')
    </body>
</html>
