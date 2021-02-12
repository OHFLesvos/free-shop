<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\App;
use Livewire\Component;

use Illuminate\Support\Str;

class WelcomePage extends Component
{
    public ?string $content = null;

    public function mount()
    {
        if (setting()->has('content.welcome_text')) {
            $value = setting()->get('content.welcome_text.' . App::getLocale(), setting()->get('content.welcome_text.' . config('app.fallback_locale')));
            $this->content = Str::of($value)->markdown();
        }
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
