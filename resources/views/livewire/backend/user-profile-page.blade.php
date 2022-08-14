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

        @livewire('backend.userprofile.user-profile-settings', ['user' => $user])
        @livewire('backend.userprofile.user-password-settings', ['user' => $user])

        <x-card title="Delete account">
            @unless($this->isLastAdmin)
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
            @else
                <x-alert type="warning" class="mb-0">You cannot remove your account as it is the only one with an administrator role.</x-alert>
            @endunless
        </x-card>

    </div>
</div>
