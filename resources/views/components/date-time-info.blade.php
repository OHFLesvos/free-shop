@props(['value'])
@isset($value)
{{ $value->toUserTimezone()->isoFormat('LLLL') }}
<small class="ms-1 text-nowrap text-muted">({{ $value->diffForHumans() }})</small>
@endisset
