<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
    @include('layouts.includes.head')
    <body class="bg-light h-100 d-flex">
        <main class="container align-self-center">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
        @include('layouts.includes.foot')
    </body>
</html>
