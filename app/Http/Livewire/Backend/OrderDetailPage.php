<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use App\Notifications\CustomerOrderCancelled;
use App\Notifications\CustomerOrderReadyed;

class OrderDetailPage extends BackendPage
{
    public Order $order;

    public $newStatus;
    public bool $showChangeStatus = false;

    protected function title()
    {
        return 'Order #' . $this->order->id;
    }

    public function mount()
    {
        $this->newStatus = $this->order->status;
    }

    public function getStatusesProperty(): array
    {
        $statuses = [ $this->order->status ];
        if ($this->order->status == 'new') {
            $statuses[] = 'ready';
            $statuses[] = 'cancelled';
        } else if ($this->order->status == 'ready') {
            $statuses[] = 'completed';
            $statuses[] = 'cancelled';
        }
        return $statuses;
    }

    public function render()
    {
        return parent::view('livewire.backend.order-detail-page');
    }

    public function submit()
    {
        if ($this->order->status != $this->newStatus) {

            if ($this->newStatus == 'completed') {
                foreach ($this->order->products as $product) {
                    if ($product->stock < $product->pivot->quantity) {
                        session()->flash('error', 'Cannot complete order; missing ' . abs($product->pivot->quantity - $product->stock) . ' ' . $product->name . ' in stock.');
                        return;
                    }
                }
                foreach ($this->order->products as $product) {
                    $product->stock -= $product->pivot->quantity;
                    $product->save();
                }
            }

            $this->order->status = $this->newStatus;
            $this->order->save();

            if ($this->order->customer != null) {
                if ($this->order->status == 'ready') {
                    $this->order->customer->notify(new CustomerOrderReadyed($this->order));
                } else if ($this->order->status == 'cancelled') {
                    $this->order->customer->notify(new CustomerOrderCancelled($this->order));
                }
            }
            $this->order->save();

            session()->flash('message', 'Order marked as ' . $this->newStatus . '.');
        }

        $this->showChangeStatus = false;
    }
}
