<?php

namespace App\Http\Livewire\Backend;

use Illuminate\View\View;

class DashboardPage extends BackendPage
{
    protected string $title = 'Dashboard';

    public function render(): View
    {
        return parent::view('livewire.backend.dashboard-page');
    }
}
