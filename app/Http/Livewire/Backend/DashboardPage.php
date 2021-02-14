<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardPage extends BackendPage
{
    protected $title = 'Dashboard';

    public function render()
    {
        return parent::view('livewire.backend.dashboard-page', [
            'newOrders' => Order::status('new')->count(),
            'readyOrders' => Order::status('ready')->count(),
            'completedOrders' => Order::status('completed')->count(),
            'registeredCustomers' => Customer::count(),
            'availableProducts' => Product::available()->count(),
            'registeredUsers' => User::count(),
        ]);
    }
}
