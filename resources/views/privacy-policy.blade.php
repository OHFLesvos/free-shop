@extends('layouts.app', ['title' => __('Privacy Policy')])

@inject('textRepo', 'App\Repository\TextBlockRepository')

@section('content')
    <div class="small-container">
        @if($textRepo->exists('privacy-policy'))
            {!! $textRepo->getMarkdown('privacy-policy') !!}
        @else
            <h1>{{ __('Privacy Policy') }}</h1>
            <p>{{ __('This content is currently not available.') }}</p>
        @endisset
    </div>
@endsection
