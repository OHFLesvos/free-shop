<?php

namespace App\Actions;

use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelOrder
{
    use AsAction;

    public function handle(Order $order): void
    {
        $order->status = 'cancelled';
        $order->save();

        if ($order->customer != null) {
            $starting_credit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
            $maximum = setting()->get('customer.credit_topup.maximum', $starting_credit);
            $order->customer->credit = max($order->customer->credit, min($order->customer->credit + $order->costs, $maximum));
            $order->customer->save();
        }

        $this->notify($order);
    }

    protected function notify(Order $order): void
    {
        // NOOP
    }
}
