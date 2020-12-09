<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public function render()
    {
        return view('livewire.backend.order-detail')
            ->layout('layouts.backend', ['title' => 'Order #' . $this->order->id]);
    }

    public function complete()
    {
        $this->order->completed_at = now();
        $this->order->save();
    }
}
