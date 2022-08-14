<form wire:submit.prevent="submit" autocomplete="off">
    <x-card title="Settings">
        @empty($user->provider)
            <div class="mb-3">
                <label for="nameInput" class="form-label">Name</label>
                <input
                    type="text"
                    class="form-control @error('user.name') is-invalid @enderror"
                    id="nameInput"
                    autocomplete="off"
                    required
                    wire:model.defer="user.name">
                @error('user.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="emailInput" class="form-label">Email address</label>
                <input
                    type="email"
                    class="form-control @error('user.email') is-invalid @enderror"
                    id="emailInput"
                    autocomplete="off"
                    required
                    wire:model.defer="user.email">
                @error('user.email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        @endif
        <div>
            <label for="timezone" class="form-label">Timezone</label>
            <div class="input-group">
                <select
                    id="timezone"
                    wire:model.defer="user.timezone"
                    class="form-select @error('timezone') is-invalid @enderror"
                    style="max-width: 20em;">
                    <option value="">- Default timezone ({{ setting()->get('timezone', config('app.timezone')) }}) -</option>
                    @foreach(listTimezones() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button
                    class="btn btn-outline-secondary"
                    type="button"
                    wire:click="detectTimezone"
                    title="Detect timezone"
                    aria-label="Detect timezone"
                >
                    <x-spinner wire:loading wire:target="detectTimezone"/>
                    <span
                        wire:loading.remove
                        wire:target="detectTimezone">
                        <x-icon icon="search-location"/>
                    </span>
                </button>
                @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <x-slot name="footer">
            <x-submit-button>Save</x-submit-button>
        </x-slot>
    </x-card>
</form>
