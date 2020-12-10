<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public Collection $relatedOrders;

    public function mount()
    {
        $this->relatedOrders = Order::query()
            ->where('id', '!=', $this->order->id)
            ->where(fn ($inner) => $inner->whereNumberCompare('customer_id_number', $this->order->customer_id_number)
                ->orWhere(fn ($inner) => $inner->whereNumberCompare('customer_phone', $this->order->customer_phone))
            )
            ->orderBy('created_at', 'desc')
            ->get();
    }

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

    public function showOrder($id)
    {
        return redirect()->route('backend.orders.show', $id);
    }
}
