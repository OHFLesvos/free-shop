<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class NavItem extends Component
{
    public array $item;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $item)
    {
        $this->item = $item;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.nav-item');
    }

    public function isActive(): bool
    {
        if (isset($this->item['active'])) {
            return $this->item['active'];
        }

        return Str::of(Request::route()->getName())->startsWith($this->item['route']);
    }
}
