<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@isset($title){{ $title }} | @endisset{{ config('app.name') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @if(setting()->has('brand.favicon') && Storage::exists(setting()->get('brand.favicon')))
        <link href="{{ url(Storage::url(setting()->get('brand.favicon'))) }}" rel="icon">
    @else
        <link href="{{ asset('favicon.ico') }}" rel="icon">
    @endif
    @livewireStyles
    @stack('styles')
</head>
