<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;

class UserEditPage extends BackendPage
{
    use AuthorizesRequests;

    public User $user;
    public $userRoles;

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
        $this->user->syncRoles($this->userRoles);

        session()->flash('message', 'User updated.');

        return redirect()->route('backend.users');
    }
}
