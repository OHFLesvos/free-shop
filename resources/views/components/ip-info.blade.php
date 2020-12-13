@props(['value'])
{{ $value }}
@php
    $hostname = App::environment() != 'local' ? gethostbyaddr($value) : null;
@endphp
@if($hostname !== null && $hostname != $value)({{ $hostname }})@endif
