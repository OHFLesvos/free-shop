<div x-data="{ shouldDelete: false }">
    <div class="small-container" x-show="shouldDelete" x-cloak>
        <x-card title="Delete account">
            <p class="card-text">Do you really want do delete your user account?</p>
            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <span>
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:loading.attr="disabled"
                            @click="shouldDelete = false">
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            wire:target="delete"
                            wire:loading.attr="disabled"
                            wire:click="delete">
                            <x-spinner wire:loading wire:target="delete"/>
                            Delete
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </div>

    <div class="small-container" x-show="!shouldDelete">
        <div class="row align-items-center mb-4 g-4">
            @isset($user->avatar)
                <div class="col-md-auto">
                    <img
                        src="{{ storage_url($user->avatar) }}"
                        alt="Avatar"
                        class="img-thumbnail"/>
                </div>
            @endisset
            <div class="col-md">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd>
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>
                    @isset($user->provider)
                        <dt class="col-sm-3">Provider</dt>
                        <dd class="col-sm-9">{{ ucfirst($user->provider) }}</dd>
                    @endisset
                    @if($user->getRoleNames()->isNotEmpty())
                        <dt class="col-sm-3">Roles</dt>
                        <dd class="col-sm-9">{{ $user->getRoleNames()->join(', ') }}</dd>
                    @endif
                    <dt class="col-sm-3">Registered</dt>
                    <dd class="col-sm-9"><x-date-time-info :value="$user->created_at"/></dd>
                    </dd>
                </dl>
            </div>
        </div>

        <x-card title="Last Login">
            <x-icon icon="clock" fixed-width class="me-1" title="Date/time" aria-label="Date/time"/> <x-date-time-info :value="$user->last_login_at"/><br>
            <x-icon icon="network-wired" fixed-width class="me-1" title="IP address" aria-label="IP address"/> <x-ip-info :value="$user->last_login_ip"/><br>
            <x-icon icon="map-marker-alt" fixed-width class="me-1" title="Location" aria-label="Location"/> <x-geo-location-info :value="$user->last_login_ip"/><br>
            <x-icon icon="desktop" fixed-width class="me-1" title="User agent" aria-label="User agent"/> <x-user-agent-info :value="$user->last_login_user_agent"/>
        </x-card>

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

        <x-card title="Delete account">
            <p>Here you can delete your account and remove all associated data from the system.</p>
            <div>
                <button
                    type="button"
                    class="btn btn-danger"
                    wire:loading.attr="disabled"
                    @click="shouldDelete = true">
                    Delete account
                </button>
            </div>
        </x-card>

    </div>
</div>
