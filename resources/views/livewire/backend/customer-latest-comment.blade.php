<div>
    @isset($lastComment)
        <x-card title="Latest comment">
            <p class="card-text">{!! nl2br(e($lastComment->content)) !!}</p>
            <p class="card-text">
                <small class="text-muted">
                    <x-date-time-info :value="$lastComment->created_at" />
                    @isset($lastComment->user)
                        {{ $lastComment->user->name }}
                    @endisset
                </small>
            </p>
            @if($hasMoreComments)
                <a href="{{ route('backend.customers.show', [$customer, 'tab' => 'comments']) }}" class="card-link">Show more</a>
            @endif
        </x-card>
    @endisset
    @livewire('components.add-comment-input')
</div>
