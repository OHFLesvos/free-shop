<?php

namespace App\Actions;

use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

class CompleteOrder
{
    use AsAction;

    public function handle(Order $order)
    {
        foreach ($order->products as $product) {
            if ($product->stock < $product->pivot->quantity) {
                $difference = abs($product->pivot->quantity - $product->stock);
                throw new \Exception('error', "Cannot complete order; missing {$difference} {$product->name} in stock.");
            }
        }
        foreach ($order->products as $product) {
            $product->stock -= $product->pivot->quantity;
            $product->save();
        }

        $order->status = 'completed';
        $order->save();
    }
}
