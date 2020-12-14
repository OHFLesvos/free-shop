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
                <x-spinner wire:loading wire:target="delete"/>
                Delete
            </button>
        </p>
    @else
        <h1 class="mb-3">User Profile</h1>
        @if(session()->has('message'))
            <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
        @endif
        <div class="row mb-4">
            <div class="col-md-auto">
                @isset($user->avatar)
                <p><img src="{{ $user->avatar }}" alt="Avatar"></p>
            @endisset
            </div>
            <div class="col-md">
                <strong>Name:</strong>
                {{ $user->name }}<br>
                <strong>E-Mail:</strong>
                {{ $user->email }}<br>
                <strong>Registered:</strong>
                <x-date-time-info :value="$user->created_at"/>
            </div>
        </div>
        <form wire:submit.prevent="submit" autocomplete="off">
            <div class="card shadow-sm mb-4">
                <div class="card-header">Profile settings</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="timezone" class="d-block">Timezone:</label>
                        <div class="input-group mb-3">
                            <select
                                id="timezone"
                                wire:model.defer="user.timezone"
                                class="custom-select @error('timezone') is-invalid @enderror"
                                style="max-width: 20em;">
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
                                    <x-spinner wire:loading wire:target="detectTimezone"/>
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
                    <p>Notifications:</p>
                    <div class="custom-control custom-checkbox mb-3">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="notifyViaEmailInput"
                            value="1"
                            wire:model.defer="user.notify_via_email">
                        <label class="custom-control-label" for="notifyViaEmailInput">Receive notifications via e-mail</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-3">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="notifyViaPhoneInput"
                            value="1"
                            wire:model.defer="user.notify_via_phone">
                        <label class="custom-control-label" for="notifyViaPhoneInput">Receive notifications via phone</label>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <x-spinner wire:loading wire:target="submit"/>
                        Save
                    </button>
                </div>
            </div>
        </form>
        @isset($user->last_login_at)
            <div class="card shadow-sm mb-4">
                <div class="card-header">Last Login</div>
                <div class="card-body">
                    <strong>Time:</strong>
                    <x-date-time-info :value="$user->last_login_at"/>
                    <br>
                    <strong>IP Address:</strong>
                    <x-ip-info :value="$user->last_login_ip"/>
                    <br>
                    <strong>Geo Location:</strong>
                    <x-geo-location-info :value="$user->last_login_ip"/>
                    <br>
                    <strong>User Agent:</strong>
                    <x-user-agent-info :value="$user->last_login_user_agent"/>
                </div>
            </div>
        @endisset
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
