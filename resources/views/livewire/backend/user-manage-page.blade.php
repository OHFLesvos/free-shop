<div x-data="{ shouldDelete: false }">
    <div class="small-container" x-show="shouldDelete" x-cloak>
        <x-card title="Delete user">
            <p class="card-text">Really delete the user <strong>{{ $user->name }}</strong> ({{ $user->email }})?</p>
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
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <x-card :title="$title">
                @isset($user->provider)
                    <h6 class="card-subtitle mb-2">{{ $user->email }} <span class="text-info">({{ ucfirst($user->provider) }})</strong></h6>
                @endisset
                @isset($user->avatar)
                    <img
                        src="{{ storage_url($user->avatar) }}"
                        alt="Avatar"
                        class="img-thumbnail mb-3"/>
                @endisset

                @empty($user->provider)
                    <div class="mb-3">
                        <label for="nameInput" class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control @error('user.name') is-invalid @enderror"
                            id="nameInput"
                            autocomplete="off"
                            required
                            @unless($user->exists) autofocus @endunless
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
                        <label for="passwordInput" class="form-label">
                            @if($user->exists) New password @else Password @endif
                        </label>
                        <div class="input-group">
                            <input
                                @if($showPassword) type="text" @else type="password" @endif
                                class="form-control @error('password') is-invalid @enderror"
                                id="passwordInput"
                                autocomplete="new-password"
                                @if(!$user->exists) required @endif
                                @if($user->exists) placeholder="Leave empty to keep current password" @endif
                                wire:model.defer="password">
                            <button type="button" class="btn btn-outline-primary" wire:click="$toggle('showPassword')">
                                @if($showPassword)
                                    <x-icon icon="eye-slash"/>
                                @else
                                    <x-icon icon="eye"/>
                                @endif
                            </button>
                            <button type="button" class="btn btn-outline-primary" wire:click="generatePassword()">Generate</button>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endempty

                <p class="form-label">Roles</p>
                @foreach($roles as $role)
                    <div class="form-check form-switch mt-2">
                        <input
                            type="checkbox"
                            class="form-check-input @error('userRoles.*') is-invalid @enderror"
                            id="role_{{ $role->id }}"
                            value="{{ $role->id }}"
                            @if($role->name == $this->adminRoleName && in_array($role->id, $this->userRoles) && App\Models\User::role($this->adminRoleName)->count() == 1) disabled @endif
                            wire:model.defer="userRoles">
                        <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                        @error('userRoles.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <small id="customerIdNumberPatternHelp" class="form-text">
                        The {{ strtolower($role->name) }}
                        @if($role->name == $this->adminRoleName)
                            has all privileges.
                        @elseif($role->permissions->isNotEmpty())
                            can {{ $role->permissions->pluck('name')->join(', ') }}.
                        @else
                            has no special privileges.
                        @endif
                    </small>
                @endforeach

                <x-slot name="footer">
                    <div class="d-flex justify-content-between">
                        <span>
                            @if($user->exists)
                                @can('delete', $user)
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        wire:loading.attr="disabled"
                                        @click="shouldDelete = true">
                                        Delete
                                    </button>
                                @endcan
                            @endif
                        </span>
                        <span>
                            @can('viewAny', App\Models\User::class)
                                <a
                                    href="{{ route('backend.users') }}"
                                    class="btn btn-link">Cancel</a>
                            @endcan
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
</div>
