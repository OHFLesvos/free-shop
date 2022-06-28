<?php

namespace App\Actions;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CancelOrder extends BaseCancelOrder
{
    protected function after(Order $order): void
    {
        Log::info('Customer cancelled order.', [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => $order->customer?->name,
            'customer.id_number' => $order->customer?->id_number,
            'customer.phone' => $order->customer?->phone,
            'customer.balance' => $order->customer?->totalBalance(),
            'order.id' => $order->id,
            'order.costs' => $order->getCostsString(),
        ]);
    }
}
