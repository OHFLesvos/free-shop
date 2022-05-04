<?php

namespace App\Actions;

use App\Dto\CostsDto;
use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class BaseCancelOrder
{
    use AsAction;

    public function handle(Order $order): void
    {
        $order->status = 'cancelled';
        $order->save();

        if ($order->customer != null) {
            $order->getCosts()->each(fn (CostsDto $costs) => $order->customer->addBalance($costs->currency_id, $costs->value));
        }

        $this->after($order);
    }

    protected abstract function after(Order $order): void;
}
