<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
    @include('layouts.includes.head')
    <body class="bg-light h-100 d-flex">
        <main class="container align-self-center">
            @yield('content')
        </main>
        @include('layouts.includes.foot')
    </body>
</html>
