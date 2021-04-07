<li class="nav-item">
    <a
        class="nav-link @if($isActive()) active @endif"
        href="{{ route($item['route']) }}"
        @if($isActive()) aria-current="page" @endif
    >
        @isset($item['icon'])
            <x-icon :icon="$item['icon']"/>
        @endisset
        {{ $item['label'] }}
    </a>
</li>