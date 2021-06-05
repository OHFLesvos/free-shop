<?php

namespace App\Http\Livewire\Backend;

class DashboardPage extends BackendPage
{
    protected string $title = 'Dashboard';

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return parent::view('livewire.backend.dashboard-page');
    }
}
