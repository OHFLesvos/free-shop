<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@isset($title){{ $title }} | @endisset{{ setting()->get('brand.name', config('app.name')) }}</title>
    @vite(['resources/js/app.js', 'resources/sass/app.scss'])
    @if(setting()->has('brand.favicon') && Storage::exists(setting()->get('brand.favicon')))
        <link href="{{ storage_url(setting()->get('brand.favicon')) }}" rel="icon">
    @else
        <link href="{{ asset('favicon.ico') }}" rel="icon">
    @endif
    @livewireStyles
    @stack('styles')
</head>
