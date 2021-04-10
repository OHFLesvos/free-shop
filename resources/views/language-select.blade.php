@extends('layouts.empty', ['title' => __('Choose your language')])

@section('content')
    <div class="small-container text-center">
        <h1 class="mb-5 display-4">{{ config('app.name') }}</h1>
        <h3>@lang('Choose your language')</h3>
        <div class="list-group shadow-sm my-4">
            @foreach($languages as $key => $value)
                <a
                    href="{{ route('languages.change', $key) }}"
                    class="list-group-item list-group-item-action py-3">
                    {{ $value }}
                </a>
            @endforeach
        </div>
    </div>
    <p class="text-center">
        <small>
            &copy;{{ now()->format('Y') }}
            <a href="https://ohf-lesvos.org" target="_blank">OHF Lesvos</a>
        </small>
    </p>
@endsection
