<?php

namespace App\Actions;

use App\Models\Order;
use App\Notifications\OrderCancelled;

class RejectOrder extends CancelOrder
{
    protected function notify(Order $order)
    {
        if ($order->customer != null) {
            $order->customer->notify(new OrderCancelled($order));
        }
    }
}
