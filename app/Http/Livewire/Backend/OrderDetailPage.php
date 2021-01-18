<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use App\Notifications\CustomerOrderCancelled;
use App\Notifications\CustomerOrderReadyed;

class OrderDetailPage extends BackendPage
{
    public Order $order;

    public bool $shouldCancel = false;
    public bool $shouldReady = false;
    public bool $shouldComplete = false;

    protected function title()
    {
        return 'Order #' . $this->order->id;
    }

    public function render()
    {
        return parent::view('livewire.backend.order-detail-page');
    }

    public function ready()
    {
        $this->order->status = 'ready';
        $this->order->save();

        $this->order->customer->notify(new CustomerOrderReadyed($this->order));

        session()->flash('message', 'Order marked as ready.');

        $this->shouldReady = false;
    }

    public function complete()
    {
        foreach ($this->order->products as $product) {
            $product->stock -= $product->pivot->quantity;
            if ($product->stock < 0) {
                session()->flash('error', 'Cannot complete order; missing ' . abs($product->stock) . ' ' . $product->name . ' in stock.');
                $this->shouldComplete = false;
                return;
            }
            $product->save();
        }

        $this->order->status = 'completed';
        $this->order->save();

        session()->flash('message', 'Order completed.');

        $this->shouldComplete = false;
    }

    public function cancel()
    {
        $this->order->status = 'cancelled';
        $this->order->save();

        $this->order->customer->notify(new CustomerOrderCancelled($this->order));

        session()->flash('message', 'Order cancelled.');

        $this->shouldCancel = false;
    }
}
