@extends('components.backend.dashboard.base-widget')

@section('title')
    <a
        href="https://www.twilio.com/console/billing"
        class="text-body text-decoration-none"
        target="_blank">
        Twilio Account
    </a>
@overwrite

@section('content')
    @isset($error)
        <x-alert type="warning" class="mb-1 mt-3">{{ $error }}</x-alert>
    @else
    <span class="@if($twilioBalance->balance < 10) text-danger @endif">
        {{ round($twilioBalance->balance, 2) }} {{ $twilioBalance->currency }} balance
    </span>
    @endisset
@overwrite
