<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;

class OrderDetailPage extends BackendPage
{
    public Order $order;

    protected function title()
    {
        return 'Order #' . $this->order->id;
    }

    public function render()
    {
        return parent::view('livewire.backend.order-detail-page');
    }
}
