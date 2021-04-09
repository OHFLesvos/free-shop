<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class FrontendPage extends Component
{
    protected $title;

    function view($view, $data = [])
    {
        return view($view, $data)
            ->layout(null, [
                'title' => method_exists($this, 'title') ? $this->title() : $this->title,
            ]);
    }
}
