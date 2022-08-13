<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginPage extends Component
{
    public String $email = '';
    public String $password = '';

    protected array $rules = [
        'email' => [
            'filled',
            'email',
        ],
        'password' => [
            'filled',
        ],
    ];

    public function render()
    {
        return view('livewire.backend.login-page', [
            'oauth' => $this->getOauthProviders(),
            'hasLocalLogin' => User::whereNull('provider')->exists(),
        ])->layout('layouts.empty', ['title' => 'Login']);
    }

    private function getOauthProviders(): array
    {
        $oauth = [];
        if (filled(config('services.google.client_id')) && filled(config('services.google.client_secret'))) {
            $oauth['google'] = [
                'url' => route('backend.login.google'),
                'label' => 'Sign in with Google',
                'domain' => config('services.google.organization_domain'),
                'icon' => 'google',
            ];
        }

        return $oauth;
    }

    public function submit()
    {
        $credentials = $this->validate();

        if (!Auth::attempt($credentials)) {
            $this->addError('email', 'Invalid username or password');
            $this->reset();
            return;
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
