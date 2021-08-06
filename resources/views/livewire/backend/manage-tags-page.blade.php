<div class="small-container">
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="card shadow-sm mb-4">
        <div class="card-header">Tags</div>
        <ul class="list-group list-group-flush">
            @forelse($tags as $tag)
                <li class="list-group-item">
                    {{ $tag->name }}
                    @unless($tag->customers->isEmpty())
                        <small class="float-end text-muted">{{ $tag->customers->count() }} customers</small>
                    @endunless
                </li>
            @empty
                <li class="list-group-item text-center">
                    <em>
                        No tags found.
                    </em>
                </li>
            @endforelse
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
                            Add
                        </button>
                        @error('newTagName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('tagAdded', () => {
            document.getElementById('newTagNameInput').focus();
        })
    </script>
@endpush