<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

abstract class BackendPage extends Component
{
    protected $title;

    function view($view, $data = [])
    {
        return view($view, $data)
            ->layout('layouts.backend', [
                'title' => method_exists($this, 'title') ? $this->title() : $this->title,
            ]);
    }
}
