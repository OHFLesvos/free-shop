@extends('components.backend.dashboard.base-widget')

@section('title')
    <a href="{{ route('backend.stock.changes') }}" class="text-body text-decoration-none">Latest stock changes</a>
@overwrite

@section('content')
    @forelse($changes as $change)
        <p class="card-text">
            {{ abs($change->quantity) }} {{ $change->product->name }}
            @if ($change->quantity > 0)
                added
            @else
                removed
            @endif
            @isset($change->user)
                by {{ optional($change->user)->name }}
            @endisset
            {{ $change->created_at->diffForHumans() }}.
        </p>
    @empty
        <em>No changes registered.</em>
    @endforelse
@overwrite
