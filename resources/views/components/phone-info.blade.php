@props(['value'])
@php
    try {
        $phoneNumber = phone($value);
        $country = $phoneNumber->getCountry();
    } catch (Throwable $ignored) {
    }
@endphp
@if (isset($phoneNumber) && isset($country))
    {{ $phoneNumber->formatInternational() }}
    <small class="ms-1 text-muted">{{ Countries::getOne($country, 'en') }}</small>
@else
    {{ $value }}
@endif
