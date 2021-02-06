@props([
    'type',
    'dismissible' => false
])
<div class="alert alert-{{ $type }} shadow-sm @if($dismissible) alert-dismissible fade show @endif" role="alert">
    @if($type == 'success')<x-icon icon="check-circle" fixed-width/>
    @elseif($type == 'info')<x-icon icon="info-circle" fixed-width/>
    @elseif($type == 'warning')<x-icon icon="exclamation-circle" fixed-width/>
    @elseif($type == 'danger')<x-icon icon="exclamation-triangle" fixed-width/>
    @endif
    {{ $slot }}
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
