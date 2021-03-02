@extends('components.backend.dashboard.base-widget')

@section('title')
    <a href="{{ route('backend.customers') }}" class="text-body text-decoration-none">Customers</a>
@overwrite

@section('content')
    {{ $registeredCustomers }} registered customers
@overwrite
