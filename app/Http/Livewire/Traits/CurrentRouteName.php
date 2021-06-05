<?php

namespace App\Http\Livewire\Traits;

use Illuminate\Support\Facades\Request;

trait CurrentRouteName
{
    public ?string $currentRouteName;

    public function mountCurrentRouteName(): void
    {
        $this->currentRouteName = Request::route()->getName();
    }
}
