@props(['value'])
@php
    $location = geoip()->getLocation($value);
@endphp
@if(!$location->default)
{{ $location->city }},
@isset($location->state_name){{ $location->state_name }},@endisset {{ $location->country }}
@else
<em>Unknown</em>
@endif
