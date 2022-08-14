<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserProfilePage extends BackendPage
{
    protected string $title = 'User Profile';

    public User $user;

    protected $listeners = ['userProfileUpdated' => 'refreshUser'];

    public function mount(): void
    {
        $this->user = Auth::user();
    }

    public function render(): View
    {
        return parent::view('livewire.backend.user-profile-page');
    }

    public function getIsLastAdminProperty(): bool
    {
        return !$this->user->hasRole(AuthServiceProvider::ADMINISTRATOR_ROLE) || User::role(AuthServiceProvider::ADMINISTRATOR_ROLE)->count() == 1;
    }

    public function refreshUser()
    {
       $this->user->refresh();
    }

    public function delete()
    {
        if ($this->getIsLastAdminProperty()) {
            return;
        }

        $this->user->delete();

        return redirect(route('backend.login'));
    }
}
