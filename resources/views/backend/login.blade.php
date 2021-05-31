@extends('layouts.empty', ['title' => 'Login'])

@section('content')
    <div class="mx-auto" style="max-width: 600px">
        <h1 class="display-4 text-center">{{ config('app.name') }}</h1>
        <h2 class="mb-4 display-6 text-center">Backend Login</h2>

        @if ($errors->any())
            @if ($errors->has('email'))
                <x-alert type="danger">
                    {{ $errors->first('email') }}
                </x-alert>
            @endif
        @endif
        @if(count($oauth) > 0)
            <p class="text-center">
                @foreach ($oauth as $service)
                    <a href="{{ $service['url'] }}" class="btn btn-primary">
                        <x-icon type="brands" :icon="$service['icon']"/>
                        {{ $service['label'] }}
                        @if($service['domain'] != null)({{ $service['domain'] }})@endif
                    </a>
                @endforeach
            </p>
        @else
            <x-alert type="warning">No OAuth provider configured.</x-alert>
        @endif

        <p class="text-center">
            <a href="{{ route('home') }}">Return to shop</a>
            | <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
        </p>
    </div>
@endsection
