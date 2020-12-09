<div class="alert alert-{{ $type }}" role="alert">
    @if($type == 'success')<x-bi-check-circle class="mr-1"/>
    @elseif($type == 'info')<x-bi-info-circle class="mr-1"/>
    @elseif($type == 'warning')<x-bi-exclamation-circle class="mr-1"/>
    @elseif($type == 'danger')<x-bi-exclamation-triangle class="mr-1"/>@endif
    {{ $slot }}
</div>
