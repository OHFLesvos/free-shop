<?php

namespace App\Http\Livewire\Backend;

use App\Events\UserRolesChanged;
use App\Models\User;
use App\Notifications\UserRolesUpdated;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserEditPage extends BackendPage
{
    use AuthorizesRequests;

    public User $user;
    public $userRoles;

    public bool $shouldDelete = false;

    public function rules()
    {
        return [
            'userRoles.*' => [
                Rule::in(Role::all()->pluck('id')),
            ]
        ];
    }

    public function mount()
    {
        $this->authorize('update', $this->user);

        $this->userRoles = $this->user->roles->pluck('id')
            ->values()
            ->map(fn ($id) => (string)$id)
            ->toArray();
    }

    protected function title()
    {
        return 'Edit User ' . $this->user->name;
    }

    public function render()
    {
        return parent::view('livewire.backend.user-edit-page', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function getAdminRoleNameProperty()
    {
        return AuthServiceProvider::ADMINISTRATOR_ROLE;
    }

    public function submit()
    {
        $this->authorize('update', $this->user);

        $this->validate();

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
