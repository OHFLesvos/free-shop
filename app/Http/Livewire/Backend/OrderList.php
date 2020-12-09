<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\Component;

class OrderList extends Component
{
    public $orders;

    public function mount()
    {
        $this->orders = Order::query()
            ->open()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.backend.order-list')
            ->layout('layouts.backend', ['title' => 'Orders']);
    }
}
