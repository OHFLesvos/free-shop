@php
$items = [
    [
        'label' => 'Settings',
        'route' => 'backend.configuration.settings',
        'authorized' => auth()
            ->user()
            ->can('update settings'),
    ],
    [
        'label' => 'Text Blocks',
        'route' => 'backend.configuration.text-blocks',
        'authorized' => auth()
            ->user()
            ->can('viewAny', App\Models\TextBlock::class),
    ],
    [
        'label' => 'Blocked Phone Numbhers',
        'route' => 'backend.configuration.blocked-phone-numbers',
        'authorized' => auth()
            ->user()
            ->can('viewAny', App\Models\BlockedPhoneNumber::class),
    ],
];
@endphp
<nav class="nav nav-pills justify-content-center mb-4">
    @foreach (collect($items)->filter(fn($item) => !isset($item['authorized']) || $item['authorized']) as $item)
        @php
            $active = Str::of($currentRouteName)->startsWith($item['route']);
        @endphp
        <a class="nav-link @if ($active) active @endif" @if ($active) aria-current="page"
    @endif
    href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
    @endforeach
</nav>
