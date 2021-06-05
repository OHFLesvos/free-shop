<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use donatj\UserAgent\UserAgentParser;
use Illuminate\Support\Collection;

class MetricsAggregator
{
    public ?string $date_start = null;
    public ?string $date_end = null;

    public function __construct(?string $date_start, ?string $date_end)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
    }

    public function customersRegistered(): int
    {
        return Customer::registeredInDateRange($this->date_start, $this->date_end)
            ->count();
    }

    public function ordersRegistered(): int
    {
        return Order::registeredInDateRange($this->date_start, $this->date_end)
            ->count();
    }

    public function ordersCompleted(): int
    {
        return Order::completedInDateRange($this->date_start, $this->date_end)
            ->count();
    }

    public function customersWithCompletedOrders(): int
    {
        return Customer::whereHas('orders', fn ($qry) => $qry->completedInDateRange($this->date_start, $this->date_end))
            ->count();
    }

    public function totalProductsHandedOut(): int
    {
        return Order::completedInDateRange($this->date_start, $this->date_end)
            ->get()
            ->map(fn ($order) => $order->numberOfProducts())
            ->sum();
    }

    public function productsHandedOut(bool $sortByQuantity = false, bool $sortDesc = false): Collection
    {
        return Product::whereHas('orders', fn ($qry) => $qry->completedInDateRange($this->date_start, $this->date_end))
            ->get()
            ->map(fn ($product) => [
                'name' => $product->name,
                'category' => $product->category,
                'sequence' => $product->sequence,
                'quantity' => $product->orders()->completedInDateRange($this->date_start, $this->date_end)->sum('quantity')
            ])
            ->sortBy($sortByQuantity
            ? [
                ['quantity', $sortDesc ? 'desc' : 'asc'],
            ]
            : [
                ['category', $sortDesc ? 'desc' : 'asc'],
                ['sequence', $sortDesc ? 'desc' : 'asc'],
                ['name', $sortDesc ? 'desc' : 'asc'],
            ])
            ->values();
    }

    public function averageOrderDuration(): ?float
    {
        return Order::completedInDateRange($this->date_start, $this->date_end)
            ->select('completed_at', 'created_at')
            ->get()
            ->map(fn ($order) => $order->completed_at->diffInDays($order->created_at))
            ->avg();
    }

    public function userAgents(): array
    {
        $parser = new UserAgentParser();
        $data = Order::registeredInDateRange($this->date_start, $this->date_end)
            ->pluck('user_agent')
            ->map(fn($value) => $parser->parse($value))
            ->map(fn($ua) => [
                'browser' => $ua->browser(),
                'os' => $ua->platform(),
            ]);
        return [
            'browser' => $data->pluck('browser')->countBy()->sortDesc(),
            'os' => $data->pluck('os')->countBy()->sortDesc(),
        ];
    }

    public function customerLocales(): Collection
    {
        return Customer::whereHas('orders', fn ($qry) => $qry->registeredInDateRange($this->date_start, $this->date_end))
            ->select('locale')
            ->selectRaw('COUNT(locale) AS cnt')
            ->whereNotNull('locale')
            ->groupBy('locale')
            ->pluck('cnt', 'locale')
            ->sortDesc();
    }
}
