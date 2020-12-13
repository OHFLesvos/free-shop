@props(['value'])
@php
    $parser = new donatj\UserAgent\UserAgentParser();
    $ua = $parser->parse($value);
@endphp
{{ $ua->browser() }} {{ $ua->browserVersion() }} on {{ $ua->platform() }}
