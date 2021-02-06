<div class="small-container">
    @isset($content)
        {!! $content !!}
    @else
        <h1>Welcome to {{ config('app.name') }}</h1>
        <p>Click the button below to see the available products.</p>
    @endisset
    <p><a href="{{ route('shop-front') }}" class="btn btn-primary">@lang('Go to the shop')</a></p>
</div>
