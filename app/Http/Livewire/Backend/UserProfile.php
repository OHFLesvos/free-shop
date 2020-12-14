<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserProfile extends Component
{
    public User $user;
    public bool $shouldDelete = false;
    public $rules = [
        'user.timezone' => [
            'nullable',
            'timezone',
        ],
        'user.notify_via_email' => 'boolean',
        'user.notify_via_phone' => 'boolean',
    ];

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.backend.user-profile')
            ->layout('layouts.backend', ['title' => 'User Profile']);
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

        session()->flash('message', 'User profile information updated.');
    }

    public function delete()
    {
        $this->user->delete();

        return redirect(route('home'));
    }
}
