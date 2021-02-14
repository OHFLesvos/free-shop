@extends('layouts.app', ['title' => __('Privacy Policy')])

@section('content')
    <div class="small-container">
        @php
            $content = App\Models\TextBlock::getAsMarkdown('privacy-policy');
        @endphp
        @isset($content)
            {!! $content !!}
        @else
            <h1>@lang('Privacy Policy')</h1>
            <p>@lang('This content is currently not available.')</p>
        @endisset
    </div>
@endsection
