<?php

namespace App\Http\Livewire\Backend\Userprofile;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Torann\GeoIP\Facades\GeoIP;

class UserProfileSettings extends Component
{
    public User $user;

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
            'user.timezone' => [
                'nullable',
                'timezone',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.backend.userprofile.user-profile-settings');
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

        $this->emit('userProfileUpdated');

        session()->flash('submitMessage', 'Your profile information has been updated.');
    }
}
