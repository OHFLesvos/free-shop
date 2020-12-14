<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Illuminate\Support\Collection;

class OrderDetailPage extends BackendPage
{
    public Order $order;

    public Collection $relatedOrders;

    public function mount()
    {
        $this->relatedOrders = Order::query()
            ->where('id', '!=', $this->order->id)
            ->whereHas('customer', function ($cqry) {
                $cqry->whereNumberCompare('id_number', $this->order->customer->id_number)
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $this->order->customer->phone));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function title()
    {
        return 'Order #' . $this->order->id;
    }

    public function render()
    {
        return parent::view('livewire.backend.order-detail-page');
    }

    public function complete()
    {
        $this->order->completed_at = now();
        $this->order->save();
    }

    public function cancel()
    {
        $this->order->cancelled_at = now();
        $this->order->save();
    }
}
