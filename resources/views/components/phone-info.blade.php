@props(['value'])
@php
try {
    $phoneNumber = Propaganistas\LaravelPhone\PhoneNumber::make($value);
    $country = $phoneNumber->getCountry();
} catch (Throwable $ignored) {}
@endphp
@if(isset($phoneNumber) && isset($country))
    {{ $phoneNumber->formatInternational() }}
    <small class="ml-1 text-muted">{{ Countries::getOne($country, 'en') }}</small>
@else
    {{ $value }}
@endif
