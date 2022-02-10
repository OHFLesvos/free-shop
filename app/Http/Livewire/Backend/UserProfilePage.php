<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Torann\GeoIP\Facades\GeoIP;

class UserProfilePage extends BackendPage
{
    protected string $title = 'User Profile';

    public User $user;

    public bool $shouldDelete = false;

    public array $rules = [
        'user.timezone' => [
            'nullable',
            'timezone',
        ],
    ];

    public function mount(): void
    {
        $this->user = Auth::user();
    }

    public function render(): View
    {
        return parent::view('livewire.backend.user-profile-page');
    }

    public function detectTimezone(): void
    {
        $geoIp = GeoIP::getLocation();
        $this->user->timezone = $geoIp['timezone'];
    }

    public function submit(): void
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
