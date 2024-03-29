@extends('components.backend.dashboard.base-widget')

@section('title')
    {{ $title }}
@overwrite

@section('content')
    <dl class="row mb-0 mt-3">
        @foreach($data as $key => $value)
            <dt class="col-sm-4">{{ $key }}</dt>
            <dd class="col-sm-8">{!! nl2br(e($value)) !!}</dd>
        @endforeach
    </dl>
@overwrite
