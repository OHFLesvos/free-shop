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

    public ?string $dateStart = null;

    public ?string $dateEnd = null;

    protected array $sortableFields = [
        'product',
        'quantity',
    ];

    /**
     * @var array
     */
    protected $queryString = [
        'dateStart' => ['except' => ''],
        'dateEnd' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('view reports');

        if (request()->has('dateStart') && request()->has('dateEnd')) {
            $this->range = 'custom';
        }

        $this->updatedRange($this->range);
    }

    public function render(): View
    {
        return parent::view('livewire.backend.reports-page', $this->getData());
    }

    public function updatedDateStart(): void
    {
        $this->range = 'custom';
    }

    public function updatedDateEnd(): void
    {
        $this->range = 'custom';
    }

    public function updatedRange(string $value): void
    {
        if ($value == 'today') {
            $this->dateStart = now()->toDateString();
            $this->dateEnd = now()->toDateString();
        } elseif ($value == 'yesterday') {
            $this->dateStart = now()->subDay()->toDateString();
            $this->dateEnd = now()->subDay()->toDateString();
        } elseif ($value == 'this_week') {
            $this->dateStart = now()->startOfWeek()->toDateString();
            $this->dateEnd = now()->toDateString();
        } elseif ($value == 'last_week') {
            $this->dateStart = now()->subWeek()->startOfWeek()->toDateString();
            $this->dateEnd = now()->subWeek()->endOfWeek()->toDateString();
        } elseif ($value == 'this_month') {
            $this->dateStart = now()->startOfMonth()->toDateString();
            $this->dateEnd = now()->toDateString();
        } elseif ($value == 'last_month') {
            $this->dateStart = now()->subMonth()->startOfMonth()->toDateString();
            $this->dateEnd = now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($value == 'this_year') {
            $this->dateStart = now()->startOfYear()->toDateString();
            $this->dateEnd = now()->toDateString();
        } elseif ($value == 'last_year') {
            $this->dateStart = now()->subYear()->startOfYear()->toDateString();
            $this->dateEnd = now()->subYear()->endOfYear()->toDateString();
        } elseif ($value == 'all_time') {
            $this->dateStart = null;
            $this->dateEnd = null;
        }
    }

    public function getDateRangeTitleProperty(): string
    {
        if (isset($this->dateStart) && isset($this->dateEnd)) {
            $dateStart = (new Carbon($this->dateStart))->isoFormat('LL');
            $dateEnd = (new Carbon($this->dateEnd))->isoFormat('LL');
            if ($dateStart != $dateEnd) {
                return "Between $dateStart and $dateEnd";
            }

            return $dateStart;
        }

        return 'All time';
    }

    private function getData(): array
    {
        $aggregator = new MetricsAggregator($this->dateStart, $this->dateEnd);

        $dateStart = new Carbon($this->dateStart);
        $dateEnd = new Carbon($this->dateEnd);
        $daysInPeriod = $dateEnd->diffInDays($dateStart);

        $ordersRegistered = $aggregator->ordersRegistered();
        $ordersCompleted = $aggregator->ordersCompleted();

        return [
            'customersRegistered' => $aggregator->customersRegistered(),
            'ordersRegistered' => $ordersRegistered,
            'averageOrdersRegisteredPerDay' => $daysInPeriod > 0 ? $ordersRegistered / $daysInPeriod : $ordersRegistered,
            'ordersCompleted' => $ordersCompleted,
            'averageOrdersCompletedPerDay' => $daysInPeriod > 0 ? $ordersCompleted / $daysInPeriod : $ordersCompleted,
            'customersWithCompletedOrders' => $aggregator->customersWithCompletedOrders(),
            'totalProductsHandedOut' => $aggregator->totalProductsHandedOut(),
            'productsHandedOut' => $aggregator->productsHandedOut($this->sortBy == 'quantity', $this->sortDirection == 'desc'),
            'averageOrderDuration' => $aggregator->averageOrderDuration(),
            'userAgents' => $aggregator->userAgents(),
            'customerLocales' => $aggregator->customerLocales(),
            'communicationChannels' => $aggregator->communicationChannels(),
        ];
    }

    public function generatePdf(): Response
    {
        $name = 'Report - ' . $this->ranges[$this->range] . ' (' . now()->toDateString() . ')';

        $data = $this->getData();
        $mergeData = [
            'dateRangeTitle' => $this->getDateRangeTitleProperty(),
        ];
        $config = [
            'title' => $name,
            'author' => setting()->get('brand.name', config('app.name')),
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
        ];

        /** @var \Mccarlosen\LaravelMpdf\LaravelMpdf $pdf */
        $pdf = PDF::loadView('backend.pdf-report', $data, $mergeData, $config);

        return response()->streamDownload(fn () => $pdf->stream(), $name . '.pdf');
    }
}
