<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\Component;

class OrderProducts extends Component
{
    public Order $order;

    public function render()
    {
        return view('livewire.backend.order-products');
    }
}
