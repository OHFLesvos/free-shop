<div class="medium-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card title="{{ $user->name }}">
            <p class="form-label">Roles</p>
            @foreach($roles as $id => $name)
                <div class="form-check form-switch">
                    <input
                        type="checkbox"
                        class="form-check-input @error('userRoles.*') is-invalid @enderror"
                        id="role_{{ $id }}"
                        value="{{ $id }}"
                        wire:model.defer="userRoles">
                    <label class="form-check-label" for="role_{{ $id }}">{{ $name }}</label>
                    @error('userRoles.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endforeach

            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    <span>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            wire:target="submit"
                            wire:loading.attr="disabled">
                            <x-spinner wire:loading wire:target="submit"/>
                            Save
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>
