<?php

namespace App\Actions;

use App\Models\Order;
use App\Notifications\OrderReadied;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadyOrder
{
    use AsAction;

    public function handle(Order $order): void
    {
        $order->status = 'ready';
        $order->save();

        if ($order->customer != null) {
            $order->customer->notify(new OrderReadied($order));
        }
    }
}
