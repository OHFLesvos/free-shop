<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;

class OrderService
{
    public function isDailyOrderMaximumReached(): bool
    {
        $maxOrdersPerDay = setting()->get('shop.max_orders_per_day', 0);
        if ($maxOrdersPerDay <= 0) {
            return false;
        }

        $currentOrderCount = Order::query()
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        return $maxOrdersPerDay <= $currentOrderCount;
    }

    /**
     * @param array<int,int> $selection
     * @return string
     */
    public function calculateTotalCostsString(array $selection): string
    {
        $value = collect($selection)
            ->filter(fn ($quantity) => $quantity > 0)
            ->map(function(int $quantity, int $productId) {
                $product = Product::find($productId);
                return [
                    'currency' => $product->currency_id,
                    'value' => $product->price * $quantity,
                ];
            })
            ->whereNotNull('currency')
            ->groupBy('currency')
            ->map(fn (Collection $v, int $k) => $v->sum('value') . ' ' . Currency::find($k)->name)
            ->join(', ');

        return filled($value) ? $value : 'Free';
    }

}
