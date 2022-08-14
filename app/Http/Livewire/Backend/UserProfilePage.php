<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Torann\GeoIP\Facades\GeoIP;

class UserProfilePage extends BackendPage
{
    protected string $title = 'User Profile';

    public User $user;

    public String $current_password = '';
    public String $password = '';
    public String $password_confirmation = '';

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

        session()->flash('submitMessage', 'Your profile information has been updated.');
    }

    public function submitPassword()
    {
        if (filled($this->user->provider)) {
            return;
        }

        $this->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $this->user->password = Hash::make($this->password);
        $this->user->save();

        $this->reset(['password', 'current_password', 'password_confirmation']);

        session()->flash('submitMessage', 'Your password has been updated.');
    }

    public function getIsLastAdminProperty(): bool
    {
        return !$this->user->hasRole(AuthServiceProvider::ADMINISTRATOR_ROLE) || User::role(AuthServiceProvider::ADMINISTRATOR_ROLE)->count() == 1;
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
