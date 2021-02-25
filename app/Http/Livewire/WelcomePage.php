<?php

namespace App\Http\Livewire;

use App\Repository\TextBlockRepository;
use Livewire\Component;

class WelcomePage extends Component
{
    public ?string $content = null;

    public function mount(TextBlockRepository $textRepo)
    {
        $this->content = $textRepo->getMarkdown('welcome');
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
