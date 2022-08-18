<div class="mx-auto" style="max-width: 400px">
    <h1 class="display-4 text-center">{{ setting()->get('brand.name', config('app.name')) }}</h1>
    <h2 class="mb-4 display-6 text-center">Backend Login</h2>

    <form wire:submit.prevent="submit" autocomplete="off">
        <x-card title="First-time user registration">
            <div class="mb-3">
                <label for="nameInput" class="form-label">Name</label>
                <input
                    type="text"
                    class="form-control @error('user.name') is-invalid @enderror"
                    id="nameInput"
                    autofocus
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
            <div class="mb-3">
                <label for="passwordInput" class="form-label">Password</label>
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
                <label for="passwordConfirmationInput" class="form-label">Confirm password</label>
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
                <x-submit-button>Create account</x-submit-button>
            </x-slot>
        </x-card>
    </form>

    <p class="text-center">
        <a href="{{ route('home') }}">Return to shop</a>
        | <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
    </p>
</div>
