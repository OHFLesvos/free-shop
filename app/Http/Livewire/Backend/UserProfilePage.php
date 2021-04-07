<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserProfilePage extends BackendPage
{
    public User $user;

    public bool $shouldDelete = false;

    public $rules = [
        'user.timezone' => [
            'nullable',
            'timezone',
        ],
    ];

    public function mount()
    {
        $this->user = Auth::user();
    }

    protected $title = 'User Profile';

    public function render()
    {
        return parent::view('livewire.backend.user-profile-page');
    }

    public function detectTimezone()
    {
        $geoIp = geoip()->getLocation();
        $this->user->timezone = $geoIp['timezone'];
    }

    public function submit()
    {
        $this->validate();

        $this->user->save();

        session()->flash('submitMessage', 'User profile information updated.');
    }

    public function delete()
    {
        $this->user->delete();

        return redirect(route('backend.login'));
    }
}
