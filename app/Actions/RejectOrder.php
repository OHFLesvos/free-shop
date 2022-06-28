<?php

namespace App\Actions;

use App\Models\Order;
use App\Notifications\OrderCancelled;

class RejectOrder extends BaseCancelOrder
{
    protected function after(Order $order): void
    {
        if ($order->customer != null) {
            $order->customer->notify(new OrderCancelled($order));
        }
    }
}
