<?php

namespace App\Http\Livewire;

use Illuminate\Mail\Markdown;
use Livewire\Component;

class WelcomePage extends Component
{
    public ?string $content = null;

    public function mount()
    {
        if (setting()->has('welcome-text')) {
            $this->content = Markdown::parse(setting()->get('welcome-text'));
        }
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
