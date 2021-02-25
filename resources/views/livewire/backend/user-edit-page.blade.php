@if($shouldDelete)
    <div class="small-container">
        <x-card title="Delete user">
            <p class="card-text">Really delete the user <strong>{{ $user->name }}</strong> ({{ $user->email }})?</p>
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
    <div class="small-container">
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <x-card title="{{ $user->name }}">
                <h6 class="card-subtitle mb-2">{{ $user->email }}</h6>
                @isset($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Avatar" class="img-thumbnail mb-3" />
                @endisset

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
                            @can('delete', $user)
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    wire:loading.attr="disabled"
                                    wire:click="$toggle('shouldDelete')">
                                    Delete
                                </button>
                            @endcan
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
@endif