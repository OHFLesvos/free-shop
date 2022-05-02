<?php

namespace App\Services;

use App\Models\Order;

class OrderManager
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
}
