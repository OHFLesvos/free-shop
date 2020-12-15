<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use App\Notifications\CustomerOrderCancelled;
use Illuminate\Support\Collection;

class OrderDetailPage extends BackendPage
{
    public Order $order;

    public Collection $relatedOrders;

    public bool $shouldCancel = false;
    public bool $shouldComplete = false;

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
        foreach ($this->order->products as $product) {
            $product->stock -= $product->pivot->quantity;
            if ($product->stock < 0) {
                session()->flash('error', 'Cannot complete order; missing ' . abs($product->stock) . ' ' . $product->name . ' in stock.');
                $this->shouldComplete = false;
                return;
            }
            $product->save();
        }

        $this->order->completed_at = now();
        $this->order->save();

        session()->flash('message', 'Order completed.');

        $this->shouldComplete = false;
    }

    public function cancel()
    {
        $this->order->cancelled_at = now();
        $this->order->save();

        $this->order->customer->notify(new CustomerOrderCancelled($this->order));

        session()->flash('message', 'Order cancelled.');

        $this->shouldCancel = false;
    }
}
