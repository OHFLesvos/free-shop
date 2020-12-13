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
        @if(config('services.google.organization_domain') != null)
            <p class="text-center">
                You can only sign in with a <strong>{{ config('services.google.organization_domain') }}</strong> organization account.
            </p>
        @endif
        <p class="text-center">
            <a href="{{ route('login.google') }}" class="btn btn-primary">
                <x-icon type="brands" icon="google"/>
                Sign in
            </a>
            <a href="{{ route('home') }}" class="btn btn-link">Cancel</a>
        </p>
    </div>
@endsection
