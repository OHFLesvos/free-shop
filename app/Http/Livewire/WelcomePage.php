<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Str;

class WelcomePage extends Component
{
    public ?string $content = null;

    public function mount()
    {
        if (setting()->has('content.welcome_text')) {
            $this->content = Str::of(setting()->get('content.welcome_text'))->markdown();
        }
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
