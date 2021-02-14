@props([
    'icon',
    'type' => 'solid',
    'spin' => false,
    'pulse' => false,
    'fixedWidth' => false
])
@php
    $prefix = 'fa';
    if ($type == 'solid') { $prefix = 'fas'; }
    elseif ($type == 'regular') { $prefix = 'far'; }
    elseif ($type == 'light') { $prefix = 'fal'; }
    elseif ($type == 'duotone') { $prefix = 'fad'; }
    elseif ($type == 'brands') { $prefix = 'fab'; }
@endphp
<i {{ $attributes->merge(['class' => $prefix . ' fa-' . $icon . ($spin ? ' fa-spin' : '').($pulse ? ' fa-pulse' : '').($fixedWidth ? ' fa-fw' : '')]) }}></i>