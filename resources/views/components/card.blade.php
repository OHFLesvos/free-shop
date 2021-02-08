@props([
    'title',
    'noFooterPadding' => false,
])
<div class="card shadow-sm mb-4">
    <div class="card-body @if($noFooterPadding) pb-0 @endif">
        <h5 class="card-title">{{ $title }}</h5>
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
