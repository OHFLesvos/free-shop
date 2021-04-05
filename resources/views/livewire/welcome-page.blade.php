@inject('textRepo', 'App\Repository\TextBlockRepository')

<div class="small-container">
    @if($textRepo->exists('welcome'))
        {!! $textRepo->getMarkdown('welcome') !!}
    @else
        <h1>Welcome to {{ config('app.name') }}</h1>
        <p>Click the button below to see the available products.</p>
    @endif
    <p><a href="{{ route('shop-front') }}" class="btn btn-primary">@lang('Go to the shop')</a></p>
</div>
