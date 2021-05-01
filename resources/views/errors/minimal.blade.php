<!DOCTYPE html>
<html lang="en" class="h-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title') | {{ config('app.name') }}</title>

        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    </head>
    <body class="bg-light h-100 d-flex">
        <main class="container align-self-center">
            <div class="d-flex w-auto align-items-center justify-content-center">
                <div class="display-1 pe-3 border-end border-3 border-dark">
                    <strong>@yield('code')</strong>
                </div>
                <div class="ps-3 display-6">
                    @yield('message')
                </div>
            </div>
            @unless (isset($hide_home_button) && $hide_home_button)
                <div class="text-center mt-5">
                    <a href="/" class="btn btn-outline-dark">Back to start page</a><br>
                </div>
            @endunless
            <p class="text-center my-5">
                {{ config('app.name') }} | @include('layouts.includes.copyright')
            </p>
        </main>
    </body>
</html>
