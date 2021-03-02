<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportsPage extends BackendPage
{
    use AuthorizesRequests;

    protected $title = 'Reports';

    public ?string $date_start = null;
    public ?string $date_end = null;

    public array $ranges = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'this_month' => 'This month',
        'last_month' => 'Last month',
        'this_year' => 'This year',
        'last_year' => 'Last year',
        'all_time' => 'All time',
        'custom' => 'Custom',
    ];

    public $range = 'this_month';

    public function mount()
    {
        $this->authorize('view reports');

        $this->updatedRange($this->range);
    }

    public function render()
    {
        return parent::view('livewire.backend.reports-page', [
            'customersRegistered' => $this->customersRegistered(),
            'ordersCompleted' => $this->ordersCompleted(),
            'customersWithCompletedOrders' => $this->customersWithCompletedOrders(),
            'productsHandedOut' => $this->productsHandedOut(),
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

    private function customersRegistered()
    {
        if (isset($this->date_start) && isset($this->date_end)) {
            return Customer::registeredInDateRange($this->date_start, $this->date_end)
                ->count();
        }
        return Customer::count();
    }

    private function ordersCompleted()
    {
        if (isset($this->date_start) && isset($this->date_end)) {
            return Order::completedInDateRange($this->date_start, $this->date_end)
                ->count();
        }
        return Order::status('completed')
            ->count();
    }

    private function customersWithCompletedOrders()
    {
        if (isset($this->date_start) && isset($this->date_end)) {
            return Customer::whereHas('orders', fn ($qry) => $qry->completedInDateRange($this->date_start, $this->date_end))
                ->count();
        }
        return Customer::whereHas('orders', fn ($qry) => $qry->status('completed'))
            ->count();
    }

    private function productsHandedOut()
    {
        if (isset($this->date_start) && isset($this->date_end)) {
            return Order::completedInDateRange($this->date_start, $this->date_end)
                ->get()
                ->map(fn ($order) => $order->numberOfProducts())
                ->sum();
        }
        return Order::status('completed')
            ->get()
            ->map(fn ($order) => $order->numberOfProducts())
            ->sum();
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
