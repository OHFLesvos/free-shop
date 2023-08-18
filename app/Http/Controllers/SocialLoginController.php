<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialLoginController extends Controller
{
    public function organizationDomains(): array
    {
        return array_filter(explode(',', config('services.google.organization_domain', '')));
    }

    public function redirectToGoogle()
    {
        $driver = Socialite::driver('google');
        $orgDomains = $this->organizationDomains();
        if (! empty($orgDomains)) {
            $driver->with(['hd' => count($orgDomains) == 1 ? $orgDomains[0] : '*']);
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

        $orgDomains = $this->organizationDomains();
        if (! empty($orgDomains) && empty(array_filter($orgDomains, fn ($orgDomain) => Str::endsWith($socialUser->getEmail(), $orgDomain)))) {
            return redirect()
                ->route('backend.login')
                ->withErrors([
                    'oauth' => [
                        sprintf('Only email addresses belonging to %s are accepted.', implode(', ', $orgDomains)),
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

        $user->avatar = $this->getAvatar($socialUser->getAvatar(), $user->avatar);

        if ($user->email_verified_at == null) {
            $user->email_verified_at = now();
        }
        if ($user->wasChanged()) {
            $user->save();
        }

        Auth::login($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    private function getAvatar(?string $newAvatar, ?string $currentAvatar): ?string
    {
        if ($newAvatar !== null) {
            if (ini_get('allow_url_fopen')) {
                $avatar = 'public/avatars/' . basename($newAvatar);
                if ($currentAvatar !== null && $avatar != $currentAvatar && Storage::exists($currentAvatar)) {
                    Storage::delete($currentAvatar);
                }
                Storage::put($avatar, file_get_contents($newAvatar), 'public');

                return $avatar;
            }

            return $newAvatar;
        }

        return null;
    }
}
