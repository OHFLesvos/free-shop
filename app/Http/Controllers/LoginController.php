<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function login(): View
    {
        return view('backend.login', [
            'oauth' => $this->getOauthProviders(),
        ]);
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('backend.login');
    }
}
