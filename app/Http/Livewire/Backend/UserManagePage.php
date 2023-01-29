<?php

namespace App\Http\Livewire\Backend;

use App\Events\UserRolesChanged;
use App\Models\User;
use App\Notifications\UserRolesUpdated;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagePage extends BackendPage
{
    use AuthorizesRequests;

    public User $user;

    public String $password = '';

    public bool $showPassword = false;

    public array $userRoles;

    public function rules(): array
    {
        return [
            'user.name' => [
                Rule::requiredIf(fn () => ! $this->user->exists || $this->user->provider === null),
            ],
            'user.email' => [
                Rule::requiredIf(fn () => ! $this->user->exists || $this->user->provider === null),
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'userRoles.*' => [
                Rule::in(Role::pluck('id')),
            ],
            'password' => [
                Rule::requiredIf(fn () => ! $this->user->exists),
                Password::defaults(),
            ],
        ];
    }

    public function mount(): void
    {
        if (isset($this->user)) {
            $this->authorize('update', $this->user);
        } else {
            $this->authorize('create', User::class);
        }

        if (! isset($this->user)) {
            $this->user = new User();
        }

        $this->userRoles = $this->user->roles->pluck('id')
            ->values()
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    protected function title(): string
    {
        return $this->user->exists
            ? 'Edit User ' . $this->user->name
            : 'Register User';
    }

    public function render(): View
    {
        return parent::view('livewire.backend.user-manage-page', [
            'title' => $this->user->exists ? 'Edit User ' . $this->user->name : 'Register User',
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function generatePassword()
    {
        $this->password = Str::random(8);
        $this->showPassword = true;
    }

    public function getAdminRoleNameProperty(): string
    {
        return AuthServiceProvider::ADMINISTRATOR_ROLE;
    }

    public function submit()
    {
        if ($this->user->exists) {
            $this->authorize('update', $this->user);
        } else {
            $this->authorize('create', User::class);
        }

        $this->validate();

        if ($this->user->email_verified_at == null) {
            $this->user->email_verified_at = now();
        }

        $passwordChanged = false;
        if (filled($this->password)) {
            $this->user->password = Hash::make($this->password);
            $passwordChanged = true;
        }

        $this->user->save();

        if ($this->user->wasRecentlyCreated) {
            event(new Registered($this->user));
        }

        $previousRoles = $this->user->getRoleNames()->toArray();

        $this->user->syncRoles($this->userRoles);

        if ($previousRoles != $this->user->getRoleNames()->toArray()) {
            UserRolesChanged::dispatch($this->user, $previousRoles);
            $this->user->notify(new UserRolesUpdated());
        }

        session()->flash('message', $this->user->wasRecentlyCreated
            ? 'User registered.'
            : 'User updated.' . ($passwordChanged ? ' The password has been changed.' : ''));

        if (Auth::user()->refresh()->cannot('viewAny', User::class)) {
            return redirect()->route('backend');
        }

        return redirect()->route('backend.users');
    }

    public function delete()
    {
        $this->authorize('delete', $this->user);

        $this->user->delete();

        session()->flash('message', 'User deleted.');

        return redirect()->route('backend.users');
    }
}
