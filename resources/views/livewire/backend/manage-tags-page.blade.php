<div class="small-container">
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="card shadow-sm mb-4">
        <div class="card-header">Tags</div>
        <ul class="list-group list-group-flush">
            @forelse($tags as $tag)
                <li class="list-group-item">
                    @if(optional($editTag)->id == $tag->id)
                        <form wire:submit.prevent="update">
                            <div class="input-group">
                                <input type="text"
                                    class="form-control @error('editTag.name') is-invalid @enderror"
                                    required
                                    placeholder="Tag..."
                                    autocomplete="off"
                                    wire:model.defer="editTag.name"
                                    wire:loading.attr="disabled">
                                <button
                                    class="btn btn-primary"
                                    type="submit"
                                    wire:loading.attr="disabled">
                                    <x-icon icon="check" title="Update"/>
                                </button>
                                <button
                                    class="btn btn-secondary"
                                    type="button"
                                    wire:click="edit(0)"
                                    wire:loading.attr="disabled">
                                    <x-icon icon="circle-xmark" title="Cancel"/>
                                </button>
                                <button
                                    class="btn btn-danger"
                                    type="button"
                                    onclick="confirm('Are you sure you want to remove the tag {{ $tag->name }}?') || event.stopImmediatePropagation()"
                                    wire:click="delete({{ $tag->id }})"
                                    wire:loading.attr="disabled">
                                    <x-icon icon="trash" title="Delete"/>
                                </button>
                                @error('editTag.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </form>
                    @else
                        <span wire:click="edit({{ $tag->id }})" class="cursor-pointer">{{ $tag->name }}</span>
                        @unless($tag->customers->isEmpty())
                            <a href="{{ route('backend.customers', ['tags[]' => $tag->slug]) }}" class="float-end text-muted">{{ $tag->customers->count() }} customers</a>
                        @endunless
                    @endif
                </li>
            @empty
                <li class="list-group-item text-center">
                    <em>
                        No tags found.
                    </em>
                </li>
            @endforelse
            @if($editTag == null)
                <li class="list-group-item">
                    <form wire:submit.prevent="submit">
                        <div class="input-group">
                            <input type="text"
                                class="form-control @error('newTagName') is-invalid @enderror"
                                required
                                id="newTagNameInput"
                                placeholder="New tag..."
                                autocomplete="off"
                                wire:model.defer="newTagName"
                                wire:target="submit"
                                wire:loading.attr="disabled">
                            <button
                                class="btn btn-primary"
                                type="submit"
                                wire:target="submit"
                                wire:loading.attr="disabled">
                                <x-icon icon="check" title="Add"/>
                            </button>
                            @error('newTagName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </form>
                </li>
            @endif
        </ul>
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('tagAdded', () => {
            document.getElementById('newTagNameInput').focus();
            showSnackbar('Tag added.')
        })
        Livewire.on('tagUpdated', () => {
            showSnackbar('Tag updated.')
        })
        Livewire.on('tagDeleted', () => {
            showSnackbar('Tag deleted.')
        })
    </script>
@endpush
