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
        <header class="app-header shadow-sm p-3 mb-4 bg-white">
            <div class="container">
                <a class="brand" href="{{ route('welcome') }}">{{ config('app.name') }}</a>
            </div>
        </header>
        <main class="app-main">
            <div class="container">
                {{ $slot }}
            </div>
        </main>
        <script src="{{ mix('js/app.js') }}" defer></script>
        @livewireScripts
        @stack('scripts')
    </body>
</html>
