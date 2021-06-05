<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

abstract class FrontendPage extends Component
{
    protected string $title;

    protected function view(string $view, ?array $data = []): View
    {
        return view($view, $data)
            ->layoutData([
                'title' => method_exists($this, 'title') ? $this->title() : $this->title,
            ]);
    }
}
