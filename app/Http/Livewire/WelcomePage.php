<?php

namespace App\Http\Livewire;

use Livewire\Component;

use GrahamCampbell\Markdown\Facades\Markdown;

class WelcomePage extends Component
{
    public ?string $content = null;

    public function mount()
    {
        if (setting()->has('welcome-text')) {
            $this->content = Markdown::convertToHtml(setting()->get('welcome-text'));
        }
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
