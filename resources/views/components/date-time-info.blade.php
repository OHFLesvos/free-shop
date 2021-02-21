@props(['value', 'lineBreak' => false])
@isset($value)
{{ $value->toUserTimezone()->isoFormat('LLLL') }}
@if($lineBreak)<br>@endif
<small class="ms-1 text-nowrap text-muted">({{ $value->diffForHumans() }})</small>
@endisset
