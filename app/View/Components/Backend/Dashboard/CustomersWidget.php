<?php

namespace App\View\Components\Backend\Dashboard;

use App\Models\Customer;
use Illuminate\View\Component;

class CustomersWidget extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.backend.dashboard.customers-widget', [
            'registeredCustomers' => Customer::count(),
        ]);
    }
}
