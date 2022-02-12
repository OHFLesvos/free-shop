<?php

namespace App\Http\Livewire;

use App\Repository\TextBlockRepository;
use Illuminate\View\View;

class AboutPage extends FrontendPage
{
    public ?string $content = null;

    protected function title(): string
    {
        return __('About');
    }

    public function mount(TextBlockRepository $textRepo): void
    {
        $this->content = $textRepo->getMarkdown('about');
    }

    public function render(): View
    {
        return parent::view('livewire.about-page');
    }
}
