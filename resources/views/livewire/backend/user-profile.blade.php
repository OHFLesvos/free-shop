<div>
    @if($shouldDelete)
        <h1 class="mb-3">Delete account</h1>
        <p>Do you really want do delete your user account?</p>
        <p class="d-flex justify-content-between">
            <button
                type="button"
                class="btn btn-outline-primary"
                wire:loading.attr="disabled"
                wire:click="$toggle('shouldDelete')">
                Cancel
            </button>
            <button
                type="button"
                class="btn btn-outline-danger"
                wire:target="delete"
                wire:loading.attr="disabled"
                wire:click="delete">
                <x-icon-progress wire:loading wire:target="delete"/>
                Delete
            </button>
        </p>
    @else
        <h1 class="mb-3">User Profile</h1>
        @if(session()->has('message'))
            <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
        @endif
        @isset($user->avatar)
            <p><img src="{{ $user->avatar }}" alt="Avatar"></p>
        @endisset
        <p>
            <strong>Name:</strong>
            {{ $user->name }}
        </p>
        <p>
            <strong>E-Mail:</strong>
            {{ $user->email }}
        </p>
        <p>
            <strong>Registered:</strong>
            {{ $user->created_at->toUserTimezone()->isoFormat('LLLL') }}
            <small class="ml-1">{{ $user->created_at->diffForHumans() }}</small>
        </p>
        <form wire:submit.prevent="submit" autocomplete="off">
            <div class="card shadow-sm mb-4">
                <div class="card-header">Profile settings</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="timezone">Timezone:</label>
                            <div class="input-group mb-3">
                                <select
                                    id="timezone"
                                    wire:model.defer="user.timezone"
                                    class="custom-select @error('timezone') is-invalid @enderror">
                                    <option value="">- Default timezone -</option>
                                    @foreach(listTimezones() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button
                                        class="btn btn-outline-secondary"
                                        type="button"
                                        wire:click="detectTimezone">
                                        <span
                                            wire:loading
                                            wire:target="detectTimezone"
                                            class="spinner-border spinner-border-sm"
                                            role="status"
                                            aria-hidden="true"></span>
                                        <span
                                            wire:loading.remove
                                            wire:target="detectTimezone">
                                            <x-icon icon="search-location"/>
                                        </span>
                                    </button>
                                </div>
                                @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                </div>
                <div class="card-footer text-right">
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <x-icon-progress wire:loading wire:target="submit"/>
                        Save
                    </button>
                </div>
            </div>
        </form>
        <p>
            <button
                type="button"
                class="btn btn-danger"
                wire:loading.attr="disabled"
                wire:click="$toggle('shouldDelete')">
                Delete account
            </button>
        </p>
    @endif
</div>
