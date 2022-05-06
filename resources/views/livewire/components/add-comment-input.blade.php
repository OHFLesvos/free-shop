<div x-data="{ open: @entangle('isEditing') }" class="mb-3">
    <form
        x-show="open"
        x-cloak
        wire:submit.prevent="saveComment"
        autocomplete="off"
    >
        <div class="mb-3">
            <textarea
                class="form-control @error('content') is-invalid @enderror"
                wire:model.defer="content"
                x-ref="input"
                rows="3"
                placeholder="Add your comment..."
                autocomplete="off"></textarea>
            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        class="btn btn-primary"
        x-show="!open"
        x-on:click="open = true; $nextTick(() => $refs.input.focus());"
    >
        Add comment
    </button>
</div>
