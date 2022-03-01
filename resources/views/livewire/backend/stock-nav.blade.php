@php
$items = [
    [
        'label' => 'Product List',
        'route' => 'backend.stock',
        'authorized' => auth()
            ->user()
            ->can('manage stock'),
    ],
    [
        'label' => 'Changes',
        'route' => 'backend.stock.changes',
        'authorized' => auth()
            ->user()
            ->can('manage stock'),
    ],
];
@endphp
<nav class="nav nav-pills justify-content-center mb-4">
    @foreach (collect($items)->filter(fn($item) => !isset($item['authorized']) || $item['authorized']) as $item)
        @php
            $active = $currentRouteName == $item['route'];
        @endphp
        <a class="nav-link @if ($active) active @endif" @if ($active) aria-current="page"
    @endif
    href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
    @endforeach
</nav>
