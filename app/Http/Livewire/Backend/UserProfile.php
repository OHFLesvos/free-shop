<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class UserProfile extends Component
{
    public User $user;

    public string $phone;
    public string $phone_country;

    public bool $shouldDelete = false;

    public $rules = [
        'user.timezone' => [
            'nullable',
            'timezone',
        ],
        'phone' => [
            'nullable',
            'phone:phone_country,mobile',
        ],
        'phone_country' => 'required_with:phone',
        'user.notify_via_email' => 'boolean',
        'user.notify_via_phone' => 'boolean',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->phone_country = setting()->get('order.default_phone_country', '');
        $this->phone = '';
        if ($this->user->phone != null) {
            try {
                $phone = PhoneNumber::make($this->user->phone);
                $this->phone_country = $phone->getCountry();
                $this->phone = $phone->formatNational();
            } catch (NumberParseException $ignored) {
                $this->phone_country = '';
                $this->phone = $this->user->phone;
            }
        }
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

        if (filled($this->phone) && isset($this->phone_country)) {
            $this->user->phone = PhoneNumber::make($this->phone, $this->phone_country)
                ->formatE164();
        } else {
            $this->user->phone = null;
        }

        $this->user->save();

        session()->flash('message', 'User profile information updated.');
    }

    public function delete()
    {
        $this->user->delete();

        return redirect(route('home'));
    }
}
