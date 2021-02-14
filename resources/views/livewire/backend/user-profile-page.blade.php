@if($shouldDelete)
    <div class="small-container">
        <x-card title="Delete account">
            <p class="card-text">Do you really want do delete your user account?</p>
            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <span>
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:loading.attr="disabled"
                            wire:click="$toggle('shouldDelete')">
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
@else
    <div class="medium-container">
        <div class="row align-items-center mb-4 g-4">
            <div class="col-md-auto">
                @isset($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Avatar" class="img-thumbnail" />
                @endisset
            </div>
            <div class="col-md">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd>
                    <dt class="col-sm-3">E-Mail</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>
                    @isset($user->phone)
                        <dt class="col-sm-3">Phone</dt>
                        <dd class="col-sm-9"><x-phone-info :value="$user->phone"/></dd>
                    @endisset
                    @if($user->getRoleNames()->isNotEmpty())
                        <dt class="col-sm-3">Roles</dt>
                        <dd class="col-sm-9">{{ $user->getRoleNames()->join(', ') }}</dd>
                    @endif
                    <dt class="col-sm-3">Registered</dt>
                    <dd class="col-sm-9"><x-date-time-info :value="$user->created_at"/></dd>
                </dl>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Profile</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">Security</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="account-tab" data-bs-toggle="tab" href="#account" role="tab" aria-controls="account" aria-selected="false">Account</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active p-3 mb-4 bg-white" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <form wire:submit.prevent="submit" autocomplete="off">

                    <h5>Profile settings</h5>
                    <div class="mb-4">
                        <label for="timezone" class="form-label">Timezone:</label>
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
                                wire:click="detectTimezone">
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

                    <h5>Notifications</h5>
                    <div class="row g-3 mb-4">
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
                                    class="form-select"
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
                                    class="form-control @error('phone') is-invalid @enderror"
                                    id="inputPhone"
                                    autocomplete="off"
                                    wire:model.defer="phone">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <x-submit-button>Save</x-submit-button>
                </form>
            </div>

            <div class="tab-pane fade p-3 mb-4 bg-white" id="security" role="tabpanel" aria-labelledby="security-tab">
                @isset($user->last_login_at)
                    <h5>Last Login</h5>
                    <div>
                        <dl class="row mb-0">
                            <dt class="col-sm-2">Time</dt>
                            <dd class="col-sm-10"><x-date-time-info :value="$user->last_login_at"/></dd>
                            <dt class="col-sm-2">IP Address</dt>
                            <dd class="col-sm-10"><x-ip-info :value="$user->last_login_ip"/></dd>
                            <dt class="col-sm-2">Geo Location</dt>
                            <dd class="col-sm-10"><x-geo-location-info :value="$user->last_login_ip"/></dd>
                            <dt class="col-sm-2">User Agent</dt>
                            <dd class="col-sm-10"><x-user-agent-info :value="$user->last_login_user_agent"/></dd>
                        </dl>
                    </div>
                @endisset
            </div>

            <div class="tab-pane fade p-3 mb-4 bg-white" id="account" role="tabpanel" aria-labelledby="account-tab">
                <h5>Account settings</h5>
                <div>
                    <button
                        type="button"
                        class="btn btn-danger"
                        wire:loading.attr="disabled"
                        wire:click="$toggle('shouldDelete')">
                        Delete account
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
