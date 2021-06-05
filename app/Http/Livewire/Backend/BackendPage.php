<?php

namespace App\Http\Livewire\Backend;

use Illuminate\View\View;
use Livewire\Component;

abstract class BackendPage extends Component
{
    protected string $title;

    protected function view(string $view, ?array $data = []): View
    {
        return view($view, $data)
            ->layout('layouts.backend', [
                'title' => method_exists($this, 'title') ? $this->title() : $this->title,
            ]);
    }
}
