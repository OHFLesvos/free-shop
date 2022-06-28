<?php

namespace App\Actions;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class ModifyOrder
{
    use AsAction;

    /**
     * @param  Order  $order
     * @param  Collection<int,int>  $items
     * @return void
     */
    public function handle(
        Order $order,
        Collection $items,
    ): void {

        // TODO: Check customer balance

        $order->assignProducts($items);

        // TODO: Update customer balance

        Log::info('Updated order.', [
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
