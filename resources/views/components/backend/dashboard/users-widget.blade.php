@extends('components.backend.dashboard.base-widget')

@section('title')
    <a href="{{ route('backend.users') }}" class="text-body text-decoration-none">Users</a>
@overwrite

@section('content')
    {{ $registeredUsers }} users registered
@overwrite
