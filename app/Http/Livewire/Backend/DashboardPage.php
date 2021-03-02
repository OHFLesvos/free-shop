<?php

namespace App\Http\Livewire\Backend;

class DashboardPage extends BackendPage
{
    protected $title = 'Dashboard';

    public function render()
    {
        return parent::view('livewire.backend.dashboard-page');
    }
}
