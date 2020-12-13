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
                {{ $user->created_at->toUserTimezone()->isoFormat('LLLL') }}
                <small class="ml-1">{{ $user->created_at->diffForHumans() }}</small>
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
        <div class="card shadow-sm mb-4">
            <div class="card-header">Last Login</div>
            <div class="card-body">
                <strong>Time:</strong>
                {{ $user->last_login_at->toUserTimezone()->isoFormat('LLLL') }}
                <small class="ml-1">{{ $user->last_login_at->diffForHumans() }}</small>
                <br>
                <strong>IP Address:</strong>
                {{ $user->last_login_ip }}
                @php
                    $hostname = App::environment() != 'local' ? gethostbyaddr($user->last_login_ip) : null;
                @endphp
                @if($hostname !== null && $hostname != $user->last_login_ip)({{ $hostname }})@endif
                <br>
                @php
                    $location = geoip()->getLocation($user->last_login_ip);
                @endphp
                <strong>Geo Location:</strong>
                {{ $location->city }}, @isset($location->state){{ $location->state }},@endisset {{ $location->country }}
                <br>
                <strong>User Agent:</strong>
                @php
                    $parser = new donatj\UserAgent\UserAgentParser();
                    $ua = $parser->parse($user->last_login_user_agent);
                @endphp
                {{ $ua->browser() }} {{ $ua->browserVersion() }} on {{ $ua->platform() }}
            </div>
        </div>
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
