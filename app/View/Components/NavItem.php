<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class NavItem extends Component
{
    public function __construct(public array $item)
    {
    }

    public function render(): View
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
