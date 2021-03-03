<?php

namespace App\Http\Livewire;

use App\Repository\TextBlockRepository;
use App\Services\MetricsAggregator;
use Carbon\Carbon;
use Livewire\Component;

class AboutPage extends Component
{
    public ?string $content = null;

    public function mount(TextBlockRepository $textRepo)
    {
        $this->content = $textRepo->getMarkdown('about');
    }

    public function render()
    {
        return view('livewire.about-page', [
            'stats' => [
                __('Current month') => $this->monthlyStats(now()->startOfMonth()),
                __('Last month') => $this->monthlyStats(now()->subMonth()->startOfMonth()),
            ],
        ]);
    }

    private function monthlyStats(Carbon $month_start)
    {
        $aggregator = new MetricsAggregator($month_start, $month_start->endOfMonth());
        return [
            'month_start' => $month_start,
            'ordersCompleted' => $aggregator->ordersCompleted(),
            'customersWithCompletedOrders' => $aggregator->customersWithCompletedOrders(),
            'totalProductsHandedOut' => $aggregator->totalProductsHandedOut(),
        ];
    }
}
