@props([
    'icon',
    'type' => 'solid',
    'spin' => false,
    'pulse' => false,
    'fixedWidth' => false
])
@php
    $prefix = 'fa';
    if ($type == 'solid') { $prefix = 'fa-solid'; }
    elseif ($type == 'regular') { $prefix = 'fa-regular'; }
    elseif ($type == 'light') { $prefix = 'fa-light'; }
    elseif ($type == 'duotone') { $prefix = 'fa-duotone'; }
    elseif ($type == 'brands') { $prefix = 'fa-brands'; }
@endphp
<i {{ $attributes->merge(['class' => $prefix . ' fa-' . $icon . ($spin ? ' fa-spin' : '').($pulse ? ' fa-pulse' : '').($fixedWidth ? ' fa-fw' : '')]) }}></i>
