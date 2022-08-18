<?php

namespace App\Http\Livewire\Backend\Userprofile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class UserPasswordSettings extends Component
{
    public User $user;

    public String $current_password = '';

    public String $password = '';

    public String $password_confirmation = '';

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.backend.userprofile.user-password-settings');
    }

    public function submitPassword()
    {
        if (filled($this->user->provider)) {
            return;
        }

        $this->user->password = Hash::make($this->password);
        $this->user->save();

        $this->reset(['password', 'current_password', 'password_confirmation']);

        session()->flash('submitMessage', 'Your password has been updated.');
    }
}
