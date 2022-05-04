<div>
    <h3>Comments</h3>

    @livewire('components.add-comment-input')

    @if ($comments->isNotEmpty())
        @foreach ($comments as $comment)
            <div class="card mb-3 shadow-sm" wire:key="comment-{{ $comment->id }}">
                <div class="card-body">
                    {{ $comment->content }}
                    @can('delete', $comment)
                        <button class="btn btn-outline-danger btn-sm float-end"
                            wire:click="deleteComment({{ $comment->id }})"
                            onclick="confirm('Are you sure you want to remove this comment?') || event.stopImmediatePropagation()">
                            Delete
                        </button>
                    @endcan
                </div>
                <div class="card-footer d-sm-flex justify-content-between">
                    <span>
                        <x-date-time-info :value="$comment->created_at" />
                    </span>
                    @isset($comment->user)
                        <small class="text-muted">{{ $comment->user->name }}</small>
                    @endisset
                </div>
            </div>
        @endforeach
        <div class="overflow-auto">{{ $comments->onEachSide(2)->links() }}</div>
    @endif
</div>
