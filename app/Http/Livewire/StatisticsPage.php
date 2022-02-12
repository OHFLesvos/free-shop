<?php

namespace App\Http\Livewire;

use App\Services\MetricsAggregator;
use Carbon\Carbon;
use Illuminate\View\View;

class StatisticsPage extends FrontendPage
{
    protected function title(): string
    {
        return __('Statistics');
    }

    public function render(): View
    {
        return parent::view('livewire.statistics-page', [
            'stats' => [
                __('Current month') => $this->monthlyStats(now()->startOfMonth()),
                __('Last month') => $this->monthlyStats(now()->subMonth()->startOfMonth()),
            ],
        ]);
    }

    private function monthlyStats(Carbon $monthStart): array
    {
        $aggregator = new MetricsAggregator($monthStart, $monthStart->clone()->endOfMonth());
        return [
            'month_start' => $monthStart,
            'ordersCompleted' => $aggregator->ordersCompleted(),
            'customersWithCompletedOrders' => $aggregator->customersWithCompletedOrders(),
            'totalProductsHandedOut' => $aggregator->totalProductsHandedOut(),
            'productsHandedOut' => $aggregator->productsHandedOut(),
            'averageOrderDuration' => $aggregator->averageOrderDuration(),
        ];
    }
}
