@if($shouldDelete)
    <div class="small-container">
        <x-card title="Delete account">
            <p>Do you really want do delete your user account?</p>
            <x-slot name="footer">
                <div class="text-end">
                    <button
                        type="button"
                        class="btn btn-link"
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
                </div>
            </x-slot>
        </x-card>
    </div>
@else
    <div class="medium-container">
        <div class="row align-items-center mb-4">
            <div class="col-md-auto">
                @isset($user->avatar)
                <img src="{{ $user->avatar }}" alt="Avatar"/>
            @endisset
            </div>
            <div class="col-md">
                <strong>Name:</strong>
                {{ $user->name }}<br>
                <strong>E-Mail:</strong>
                {{ $user->email }}<br>
                @isset($user->phone)
                    <strong>Phone:</strong>
                    <x-phone-info :value="$user->phone"/><br>
                @endisset
                <strong>Registered:</strong>
                <x-date-time-info :value="$user->created_at"/>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Profile</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Security</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Account</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active mt-3" id="home" role="tabpanel" aria-labelledby="home-tab">
                <form wire:submit.prevent="submit" autocomplete="off">
                    <x-card title="Profile settings">
                        <label for="timezone" class="form-label">Timezone:</label>
                        <div class="input-group">
                            <select
                                id="timezone"
                                wire:model.defer="user.timezone"
                                class="form-select bg-light @error('timezone') is-invalid @enderror"
                                style="max-width: 20em;">
                                <option value="">- Default timezone ({{ setting()->get('timezone', config('app.timezone')) }}) -</option>
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
                    </x-card>

                    <x-card title="Notifications">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="notifyViaEmailInput"
                                        value="1"
                                        wire:model.defer="user.notify_via_email">
                                    <label class="form-check-label" for="notifyViaEmailInput">Receive notifications via e-mail</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="notifyViaPhoneInput"
                                        value="1"
                                        wire:model.defer="user.notify_via_phone">
                                    <label class="form-check-label" for="notifyViaPhoneInput">Receive notifications via phone</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="inputPhone" class="form-label">Phone:</label>
                                <div class="input-group">
                                    @php
                                        $phoneContryCodes = megastruktur\PhoneCountryCodes::getCodesList();
                                    @endphp
                                    <select
                                        class="form-select bg-light"
                                        style="max-width: 11em;"
                                        wire:model.defer="phone_country">
                                        <option value="" selected>-- Select country --</option>
                                        @foreach(Countries::getList() as $key => $val)
                                            <option value="{{ $key }}">
                                                {{ $val }}
                                                @isset($phoneContryCodes[$key] )({{ $phoneContryCodes[$key] }})@endisset
                                            </option>
                                        @endforeach
                                    </select>
                                    <input
                                        type="tel"
                                        class="form-control bg-light @error('phone') is-invalid @enderror"
                                        id="inputPhone"
                                        autocomplete="off"
                                        wire:model.defer="phone">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </x-card>
                    <p class="mb-4">
                        <x-submit-button>Save</x-submit-button>
                    </p>
                </form>
            </div>

            <div class="tab-pane fade mt-3" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                @isset($user->last_login_at)
                    <x-card title="Last Login">
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
                    </x-card>
                @endisset
            </div>

            <div class="tab-pane fade mt-3" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <x-card title="Account settings">
                    <button
                        type="button"
                        class="btn btn-danger"
                        wire:loading.attr="disabled"
                        wire:click="$toggle('shouldDelete')">
                        Delete account
                    </button>
                </x-card>
            </div>
        </div>
    </div>
@endif
