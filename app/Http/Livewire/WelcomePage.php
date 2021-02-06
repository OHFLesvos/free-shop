<?php

namespace App\Http\Livewire;

use Livewire\Component;

use GrahamCampbell\Markdown\Facades\Markdown;

class WelcomePage extends Component
{
    public ?string $content = null;

    public function mount()
    {
        if (setting()->has('content.welcome_text')) {
            $this->content = Markdown::convertToHtml(setting()->get('content.welcome_text'));
        }
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
