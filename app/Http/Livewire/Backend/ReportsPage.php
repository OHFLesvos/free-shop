<?php

namespace App\Http\Livewire\Backend;

use App\Services\MetricsAggregator;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportsPage extends BackendPage
{
    use AuthorizesRequests;
    use WithSorting;

    protected $title = 'Reports';

    public ?string $date_start = null;
    public ?string $date_end = null;

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

    public $range = 'this_month';

    public string $sortBy = 'product';
    public string $sortDirection = 'asc';
    protected $sortableFields = [
        'product',
        'quantity',
    ];

    public function mount()
    {
        $this->authorize('view reports');

        $this->updatedRange($this->range);
    }

    public function render()
    {
        $aggregator = new MetricsAggregator($this->date_start, $this->date_end);

        return parent::view('livewire.backend.reports-page', [
            'customersRegistered' => $aggregator->customersRegistered(),
            'ordersRegistered' => $aggregator->ordersRegistered(),
            'ordersCompleted' => $aggregator->ordersCompleted(),
            'customersWithCompletedOrders' => $aggregator->customersWithCompletedOrders(),
            'totalProductsHandedOut' => $aggregator->totalProductsHandedOut(),
            'productsHandedOut' => $aggregator->productsHandedOut($this->sortBy == 'quantity', $this->sortDirection == 'desc'),
            'averageOrderDuration' => $aggregator->averageOrderDuration(),
        ]);
    }

    public function updatedDateStart($value)
    {
        $this->range = 'custom';
    }

    public function updatedDateEnd($value)
    {
        $this->range = 'custom';
    }

    public function updatedRange($value)
    {
        if ($value == 'today') {
            $this->date_start = now()->toDateString();
            $this->date_end = now()->toDateString();
        } else if ($value == 'yesterday') {
            $this->date_start = now()->subDay()->toDateString();
            $this->date_end = now()->subDay()->toDateString();
        } else if ($value == 'this_week') {
            $this->date_start = now()->startOfWeek()->toDateString();
            $this->date_end = now()->toDateString();
        } else if ($value == 'last_week') {
            $this->date_start = now()->subWeek()->startOfWeek()->toDateString();
            $this->date_end = now()->subWeek()->endOfWeek()->toDateString();
        } else if ($value == 'this_month') {
            $this->date_start = now()->startOfMonth()->toDateString();
            $this->date_end = now()->toDateString();
        } else if ($value == 'last_month') {
            $this->date_start = now()->subMonth()->startOfMonth()->toDateString();
            $this->date_end = now()->subMonth()->endOfMonth()->toDateString();
        } else if ($value == 'this_year') {
            $this->date_start = now()->startOfYear()->toDateString();
            $this->date_end = now()->toDateString();
        } else if ($value == 'last_year') {
            $this->date_start = now()->subYear()->startOfYear()->toDateString();
            $this->date_end = now()->subYear()->endOfYear()->toDateString();
        } else if ($value == 'all_time') {
            $this->date_start = null;
            $this->date_end = null;
        }
    }

    public function getDateRangeTitleProperty()
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
}
