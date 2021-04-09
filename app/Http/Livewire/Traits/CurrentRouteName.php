<?php

namespace App\Http\Livewire\Traits;

use Illuminate\Support\Facades\Request;

trait CurrentRouteName
{
    public $currentRouteName;

    public function mountCurrentRouteName()
    {
        $this->currentRouteName = Request::route()->getName();
    }
}
