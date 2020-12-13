@props(['value'])
{{ $value->toUserTimezone()->isoFormat('LLLL') }}
<small class="ml-1">{{ $value->diffForHumans() }}</small>
