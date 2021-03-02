<?php

namespace App\View\Components\Backend\Dashboard;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class OrdersWidget extends Component
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
        if (Auth::user()->can('viewAny', App\Models\Order::class)) {
            return view('components.backend.dashboard.orders-widget', [
                'newOrders' => Order::status('new')->count(),
                'readyOrders' => Order::status('ready')->count(),
                'completedOrders' => Order::status('completed')->count(),
            ]);
        }
        return null;
    }
}
