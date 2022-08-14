
@empty($user->provider)
    <form wire:submit.prevent="submitPassword" autocomplete="off">
        <x-card title="Change password">
            <div class="mb-3">
                <label for="currentPasswordInput" class="form-label">Current password</label>
                <input
                    type="password"
                    class="form-control @error('current_password') is-invalid @enderror"
                    id="currentPasswordInput"
                    autocomplete="current-password"
                    required
                    wire:model.defer="current_password">
                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="passwordInput" class="form-label">New password</label>
                <input
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    id="passwordInput"
                    autocomplete="new-password"
                    required
                    wire:model.defer="password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="">
                <label for="passwordConfirmationInput" class="form-label">Confirm new password</label>
                <input
                    type="password"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    id="passwordConfirmationInput"
                    autocomplete="new-password"
                    required
                    wire:model.defer="password_confirmation">
                @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <x-slot name="footer">
                <x-submit-button>Save</x-submit-button>
            </x-slot>
        </x-card>
    </form>
@endif
