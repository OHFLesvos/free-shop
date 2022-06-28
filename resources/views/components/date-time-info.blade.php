@props(['value', 'lineBreak' => false])
@isset($value)
{{ $value->toUserTimezone()->isoFormat('LLLL') }}
@if($lineBreak)<br>@endif
<small class="text-nowrap text-muted">({{ $value->diffForHumans() }})</small>
@endisset
