<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    public function organizationDomain(): ?string
    {
        return config('services.google.organization_domain');
    }

    public function redirectToGoogle()
    {
        $driver = Socialite::driver('google');
        if ($this->organizationDomain() != null) {
            $driver->with(['hd' => $this->organizationDomain()]);
        }
        return $driver->redirect();
    }

    public function processGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $exception) {
            return redirect()
                ->route('backend.login')
                ->withErrors([
                    'email' => [
                        'Google Login failed, please try again.',
                    ],
                ]);
        }

        $orgDomain = $this->organizationDomain();
        if ($orgDomain != null && ! Str::endsWith($socialUser->getEmail(), $orgDomain)) {
            return redirect()
                ->route('backend.login')
                ->withErrors([
                    'email' => [
                        sprintf('Only %s email addresses are accepted.', $orgDomain),
                    ],
                ]);
        }

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'password' => Str::random(32),
                'provider' => 'google',
            ]
        );
        if ($user->wasRecentlyCreated) {
            event(new Registered($user));
        }

        $user->name = $socialUser->getName();

        if ($socialUser->getAvatar() !== null) {
            $avatar = 'public/avatars/' . basename($socialUser->getAvatar());
            if ($user->avatar !== null && $avatar != $user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
            Storage::put($avatar, file_get_contents($socialUser->getAvatar()));
            $user->avatar = $avatar;
        } else {
            $user->avatar = null;
        }

        if ($user->email_verified_at == null) {
            $user->email_verified_at = now();
        }
        if ($user->wasChanged()) {
            $user->save();
        }

        Auth::login($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
