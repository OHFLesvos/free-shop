@props([
    'title',
    'noFooterPadding' => false,
])
<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}>
    @isset($header)
        <div class="card-header">
            {{ $header }}
        </div>
    @endisset
    <div class="card-body @if($noFooterPadding) pb-0 @endif">
        <h5 class="card-title">{{ $title }}</h5>
        {{ $slot }}
    </div>
    @isset($addon)
        {{ $addon }}
    @endisset
    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
