@extends('components.backend.dashboard.base-widget')

@section('title')
    <a href="{{ route('backend.stock.changes') }}" class="text-body text-decoration-none">Latest stock changes</a>
@overwrite

@section('content')
    @forelse($changes as $change)
        <p class="card-text"><small>
            {{ abs($change->quantity) }} <strong>{{ $change->product->name }}</strong>
            @if ($change->quantity > 0)
                <span class="text-success">added</span>
            @else
                <span class="text-danger">removed</span>
            @endif
            @isset($change->user)
                by {{ optional($change->user)->name }}
            @endisset
            {{ $change->created_at->diffForHumans() }}.
            </small>
        </p>
    @empty
        <em>No changes registered.</em>
    @endforelse
@overwrite
