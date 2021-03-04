<?php

namespace App\View\Components\Backend\Dashboard;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class CustomersWidget extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (Auth::user()->can('viewAny', Customer::class)) {
            return view('components.backend.dashboard.customers-widget', [
                'registeredCustomers' => Customer::count(),
            ]);
        }
        return null;
    }
}
