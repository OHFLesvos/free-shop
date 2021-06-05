<?php

namespace App\Http\Livewire;

use App\Repository\TextBlockRepository;
use App\Services\MetricsAggregator;
use Carbon\Carbon;
use Illuminate\View\View;

class AboutPage extends FrontendPage
{
    public ?string $content = null;

    protected function title(): string
    {
        return __('About');
    }

    public function mount(TextBlockRepository $textRepo): void
    {
        $this->content = $textRepo->getMarkdown('about');
    }

    public function render(): View
    {
        return parent::view('livewire.about-page', [
            'stats' => [
                __('Current month') => $this->monthlyStats(now()->startOfMonth()),
                __('Last month') => $this->monthlyStats(now()->subMonth()->startOfMonth()),
            ],
        ]);
    }

    private function monthlyStats(Carbon $month_start): array
    {
        $aggregator = new MetricsAggregator($month_start, $month_start->clone()->endOfMonth());
        return [
            'month_start' => $month_start,
            'ordersCompleted' => $aggregator->ordersCompleted(),
            'customersWithCompletedOrders' => $aggregator->customersWithCompletedOrders(),
            'totalProductsHandedOut' => $aggregator->totalProductsHandedOut(),
            'productsHandedOut' => $aggregator->productsHandedOut(),
            'averageOrderDuration' => $aggregator->averageOrderDuration(),
        ];
    }
}
