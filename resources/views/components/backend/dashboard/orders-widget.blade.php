@extends('components.backend.dashboard.base-widget')

@section('title')
    <a href="{{ route('backend.orders') }}" class="text-body text-decoration-none">Orders</a>
@overwrite

@section('content')
    @if($newOrders > 0)
        {{ $newOrders }} new orders
        <br>
    @endif
    @if($readyOrders > 0)
        {{ $readyOrders }} orders ready for pickup
        <br>
    @endif
    @if($completedOrders > 0)
        {{ $completedOrders }} orders completed
    @endif
@overwrite
