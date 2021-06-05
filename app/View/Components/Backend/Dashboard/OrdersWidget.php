<?php

namespace App\View\Components\Backend\Dashboard;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class OrdersWidget extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (Auth::user()->can('viewAny', Order::class)) {
            return view('components.backend.dashboard.orders-widget', [
                'newOrders' => Order::status('new')->count(),
                'readyOrders' => Order::status('ready')->count(),
                'completedOrders' => Order::status('completed')->count(),
            ]);
        }
        return '';
    }
}
