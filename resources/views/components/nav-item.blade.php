<li class="nav-item">
    <a
        {{ $attributes->merge(['class' => 'nav-link' . ($isActive() ? ' active' : '')]) }}
        href="{{ route($item['route']) }}"
        @if($isActive()) aria-current="page" @endif
    >
        @isset($item['icon'])
            <x-icon :icon="$item['icon']" class="fa-fw"/>
        @endisset
        {{ $item['label'] }}
        @isset($attributes['target'])
            <small><x-icon icon="external-link-alt"/></small>
        @endif
    </a>
</li>