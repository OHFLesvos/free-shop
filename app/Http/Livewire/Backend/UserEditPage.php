<?php

namespace App\Http\Livewire\Backend;

use App\Events\UserRolesChanged;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserEditPage extends BackendPage
{
    use AuthorizesRequests;

    public User $user;
    public $userRoles;

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
        $this->authorize('manage users');

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
            'roles' => Role::orderBy('name')->get()->pluck('name', 'id'),
        ]);
    }

    public function submit()
    {
        $this->validate();

        $previousRoles = $this->user->getRoleNames()->toArray();

        $this->user->syncRoles($this->userRoles);

        if ($previousRoles != $this->user->getRoleNames()->toArray()) {
            UserRolesChanged::dispatch($this->user, $previousRoles);
        }

        session()->flash('message', 'User updated.');

        return redirect()->route('backend.users');
    }
}
