@extends('components.backend.dashboard.base-widget')

@section('title')
    System
@overwrite

@section('content')
    <dl class="row mb-0 mt-3">
        @foreach($data as $key => $value)
            <dt class="col-sm-4">{{ $key }}</dt>
            <dd class="col-sm-8">{{ $value }}</dd>
        @endforeach
    </dl>
@overwrite
