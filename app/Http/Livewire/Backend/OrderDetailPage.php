<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use App\Notifications\OrderReadyed;
use App\Notifications\OrderCancelled;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class OrderDetailPage extends BackendPage
{
    use AuthorizesRequests;

    public Order $order;

    public $newStatus;
    public bool $showChangeStatus = false;

    protected function title()
    {
        return 'Order #' . $this->order->id;
    }

    public function mount()
    {
        $this->authorize('view', $this->order);

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
        $this->authorize('update', $this->order);

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
                try {
                    if ($this->order->status == 'ready') {
                        $this->order->customer->notify(new OrderReadyed($this->order));
                    } else if ($this->order->status == 'cancelled') {
                        // TODO handle cancelled calculations of credits
                        $this->order->customer->notify(new OrderCancelled($this->order));
                    }
                } catch (\Exception $ex) {
                    Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
                }
            }

            $this->order->save();

            session()->flash('message', 'Order marked as ' . $this->newStatus . '.');
        }

        $this->showChangeStatus = false;
    }
}
