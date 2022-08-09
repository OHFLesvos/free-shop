<?php

namespace App\Http\Livewire\Backend;

use App\Events\UserRolesChanged;
use App\Models\User;
use App\Notifications\UserRolesUpdated;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserEditPage extends BackendPage
{
    use AuthorizesRequests;

    public User $user;

    public array $userRoles;

    public function rules(): array
    {
        return [
            'user.name' => [
                Rule::requiredIf(fn () => $this->user->provider === null),
            ],
            'user.email' => [
                Rule::requiredIf(fn () => $this->user->provider === null),
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'userRoles.*' => [
                Rule::in(Role::pluck('id')),
            ],
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->user);

        $this->userRoles = $this->user->roles->pluck('id')
            ->values()
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    protected function title(): string
    {
        return 'Edit User '.$this->user->name;
    }

    public function render(): View
    {
        return parent::view('livewire.backend.user-edit-page', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function getAdminRoleNameProperty(): string
    {
        return AuthServiceProvider::ADMINISTRATOR_ROLE;
    }

    public function submit()
    {
        $this->authorize('update', $this->user);

        $this->validate();

        $this->user->save();

        $previousRoles = $this->user->getRoleNames()->toArray();

        $this->user->syncRoles($this->userRoles);

        if ($previousRoles != $this->user->getRoleNames()->toArray()) {
            UserRolesChanged::dispatch($this->user, $previousRoles);
            $this->user->notify(new UserRolesUpdated());
        }

        session()->flash('message', 'User updated.');

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
