<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class FirstUserRegistrationPage extends Component
{
    public User $user;

    public String $password = '';

    public String $password_confirmation = '';

    public function rules(): array
    {
        return [
            'user.name' => [
                'required',
            ],
            'user.email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }

    public function mount()
    {
        if (User::exists()) {
            return redirect()->route('backend.login');
        }

        $this->user = new User();
    }

    public function render()
    {
        return view('livewire.backend.first-user-registration-page')
            ->layout('layouts.empty', ['title' => 'User registration']);
    }

    public function submit()
    {
        if (User::exists()) {
            return redirect()->route('backend.login');
        }

        $this->validate();

        $this->user->password = Hash::make($this->password);
        $this->user->save();

        event(new Registered($this->user));

        Auth::login($this->user);

        return redirect()->route('backend');
    }
}
