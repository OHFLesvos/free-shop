@props(['value'])
@isset($value)
{{ $value->toUserTimezone()->isoFormat('LLLL') }}
<small class="ms-1 text-nowrap">({{ $value->diffForHumans() }})</small>
@endisset
