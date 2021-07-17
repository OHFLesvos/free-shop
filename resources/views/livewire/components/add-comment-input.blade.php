<div x-data="{ open: @entangle('showAddComment') }" class="mb-3">
    <form
        x-show="open"
        wire:submit.prevent="saveComment"
        autocomplete="off"
    >
        <div class="mb-3">
            <textarea
                class="form-control @error('newComment') is-invalid @enderror"
                id="newCommentInput"
                wire:model.defer="newComment"
                x-ref="input"
                rows="3"
                placeholder="Add your comment..."
                autocomplete="off"></textarea>
            @error('newComment') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button
            class="btn btn-primary"
            type="submit"
        >
            Save
        </button>
        <button
            type="button"
            class="btn btn-link"
            x-on:click="open = false"
        >
            Cancel
        </button>
    </form>
    <button
        class="btn btn-secondary"
        x-show="!open"
        x-on:click="open = true; $nextTick(() => $refs.input.focus());"
    >
        Add comment
    </button>
</div>
