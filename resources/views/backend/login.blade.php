@extends('layouts.empty', ['title' => 'Login'])

@section('content')
    <div class="mx-auto" style="max-width: 600px">
        <h1 class="mb-4 display-4 text-center">Login</h1>

        @if ($errors->any())
            @if ($errors->has('email'))
                <x-alert type="danger">
                    {{ $errors->first('email') }}
                </x-alert>
            @endif
        @endif

        @if(filled(config('services.google.redirect')))
            <p class="text-center">
                <a href="{{ route('backend.login.google') }}" class="btn btn-primary">
                    <x-icon type="brands" icon="google"/>
                    Sign in with Google
                    @if(config('services.google.organization_domain') != null)
                    ({{ config('services.google.organization_domain') }})
                    @endif
                </a>
            </p>
        @endif

        <p class="text-center">
            <a href="{{ route('home') }}">Return to shop</a>
            | <a href="{{ route('privacy-policy') }}">@lang('Privacy Policy')</a>
        </p>
    </div>
@endsection
