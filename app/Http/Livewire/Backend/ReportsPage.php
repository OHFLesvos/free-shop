<?php

namespace App\Http\Livewire\Backend;

use App\Services\MetricsAggregator;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use PDF;
use Symfony\Component\HttpFoundation\Response;

class ReportsPage extends BackendPage
{
    use AuthorizesRequests;
    use WithSorting;

    protected string $title = 'Reports';

    public array $ranges = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'this_week' => 'This week',
        'last_week' => 'Last week',
        'this_month' => 'This month',
        'last_month' => 'Last month',
        'this_year' => 'This year',
        'last_year' => 'Last year',
        'all_time' => 'All time',
        'custom' => 'Custom',
    ];

    public string $range = 'this_month';
    public string $sortBy = 'product';
    public string $sortDirection = 'asc';
    public ?string $date_start = null;
    public ?string $date_end = null;

    protected array $sortableFields = [
        'product',
        'quantity',
    ];

    public function mount(): void
    {
        $this->authorize('view reports');

        $this->updatedRange($this->range);
    }

    public function render(): View
    {
        return parent::view('livewire.backend.reports-page', $this->getData());
    }

    public function updatedDateStart(?string $value): void
    {
        $this->range = 'custom';
    }

    public function updatedDateEnd(?string $value): void
    {
        $this->range = 'custom';
    }

    public function updatedRange(string $value): void
    {
        if ($value == 'today') {
            $this->date_start = now()->toDateString();
            $this->date_end = now()->toDateString();
        } elseif ($value == 'yesterday') {
            $this->date_start = now()->subDay()->toDateString();
            $this->date_end = now()->subDay()->toDateString();
        } elseif ($value == 'this_week') {
            $this->date_start = now()->startOfWeek()->toDateString();
            $this->date_end = now()->toDateString();
        } elseif ($value == 'last_week') {
            $this->date_start = now()->subWeek()->startOfWeek()->toDateString();
            $this->date_end = now()->subWeek()->endOfWeek()->toDateString();
        } elseif ($value == 'this_month') {
            $this->date_start = now()->startOfMonth()->toDateString();
            $this->date_end = now()->toDateString();
        } elseif ($value == 'last_month') {
            $this->date_start = now()->subMonth()->startOfMonth()->toDateString();
            $this->date_end = now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($value == 'this_year') {
            $this->date_start = now()->startOfYear()->toDateString();
            $this->date_end = now()->toDateString();
        } elseif ($value == 'last_year') {
            $this->date_start = now()->subYear()->startOfYear()->toDateString();
            $this->date_end = now()->subYear()->endOfYear()->toDateString();
        } elseif ($value == 'all_time') {
            $this->date_start = null;
            $this->date_end = null;
        }
    }

    public function getDateRangeTitleProperty(): string
    {
        if (isset($this->date_start) && isset($this->date_end)) {
            $date_start = (new Carbon($this->date_start))->isoFormat('LL');
            $date_end = (new Carbon($this->date_end))->isoFormat('LL');
            if ($date_start != $date_end) {
                return "Between $date_start and $date_end";
            }
            return $date_start;
        }
        return 'All time';
    }

    private function getData(): array
    {
        $aggregator = new MetricsAggregator($this->date_start, $this->date_end);
        return [
            'customersRegistered' => $aggregator->customersRegistered(),
            'ordersRegistered' => $aggregator->ordersRegistered(),
            'ordersCompleted' => $aggregator->ordersCompleted(),
            'customersWithCompletedOrders' => $aggregator->customersWithCompletedOrders(),
            'totalProductsHandedOut' => $aggregator->totalProductsHandedOut(),
            'productsHandedOut' => $aggregator->productsHandedOut($this->sortBy == 'quantity', $this->sortDirection == 'desc'),
            'averageOrderDuration' => $aggregator->averageOrderDuration(),
            'userAgents' => $aggregator->userAgents(),
            'customerLocales' => $aggregator->customerLocales(),
        ];
    }

    public function generatePdf(): Response
    {
        $name = 'Report - ' . $this->ranges[$this->range] . ' ('. now()->toDateString() . ')';

        $data = $this->getData();
        $mergeData = [
            'dateRangeTitle' => $this->getDateRangeTitleProperty(),
        ];
        $config = [
            'title' => $name,
            'author' => config('app.name'),
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
        ];

        $pdf = PDF::loadView('backend.pdf-report', $data, $mergeData, $config);

        return response()->streamDownload(fn () => $pdf->stream(), $name . '.pdf');
    }
}
