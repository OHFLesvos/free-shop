<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\StockChange;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;

class CompleteOrder
{
    use AsAction;

    public function handle(Order $order): void
    {
        foreach ($order->products as $product) {
            if ($product->stock < $product->pivot->quantity) {
                $difference = abs($product->pivot->quantity - $product->stock);
                throw new Exception("Cannot complete order; missing {$difference} {$product->name} in stock.");
            }
        }
        foreach ($order->products as $product) {
            $quantity = $product->pivot->quantity;
            $product->stock -= $quantity;
            $product->save();

            $change = new StockChange();
            $change->quantity = -$quantity;
            $change->total = $product->stock;
            $change->product()->associate($product);
            $change->order()->associate($order);
            $change->user()->associate(Auth::user());
            $change->save();
        }

        $order->status = 'completed';
        $order->save();
    }
}
