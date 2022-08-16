@extends('layouts.empty', ['title' => __('Choose your language')])

@section('content')
    <div class="small-container text-center">
        <h1 class="mb-5 display-4">{{ config('app.name') }}</h1>
        <h3>{{ __('Choose your language') }}</h3>
        <div class="list-group shadow-sm my-4">
            @foreach($languages as $key => $value)
                <a
                    href="{{ route('languages.update', $key) }}"
                    class="list-group-item list-group-item-action py-3">
                    {{ $value }}
                </a>
            @endforeach
        </div>
    </div>
    <footer class="text-center my-4">
        <small>
            @include('layouts.includes.copyright')
        </small>
    </footer>
@endsection
