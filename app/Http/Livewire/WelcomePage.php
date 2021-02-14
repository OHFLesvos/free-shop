<?php

namespace App\Http\Livewire;

use App\Models\TextBlock;
use Livewire\Component;

class WelcomePage extends Component
{
    public ?string $content = null;

    public function mount()
    {
        $this->content = TextBlock::getAsMarkdown('welcome');
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
