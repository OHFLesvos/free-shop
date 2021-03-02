@extends('components.backend.dashboard.base-widget')

@section('title')
    <a href="{{ route('backend.products') }}" class="text-body text-decoration-none">Products</a>
@overwrite

@section('content')
    {{ $availableProducts }} products available
@overwrite
