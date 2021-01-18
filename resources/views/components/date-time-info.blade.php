@props(['value'])
@isset($value)
{{ $value->toUserTimezone()->isoFormat('LLLL') }}
<small class="ml-1 text-nowrap">({{ $value->diffForHumans() }})</small>
@endisset
