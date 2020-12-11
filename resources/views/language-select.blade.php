@extends('layouts.empty', ['title' => __('Choose your language')])

@section('content')
    <div class="text-center mx-auto" style="max-width: 600px">
        <h3>@lang('Choose your language')</h3>
        <div class="list-group shadow-sm my-4">
            @foreach($languages as $key => $value)
                <a href="{{ route('languages.change', $key) }}" class="list-group-item list-group-item-action">
                    {{ $value }}
                </a>
            @endforeach
        </div>
    </div>
@endsection
