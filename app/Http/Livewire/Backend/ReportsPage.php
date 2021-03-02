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

    public string $date_start;
    public string $date_end;

    public bool $all_time = false;

    public function mount()
    {
        $this->authorize('view reports');

        $this->date_start = now()->subDays(30)->toDateString();
        $this->date_end = now()->toDateString();
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

    private function customersRegistered()
    {
        if (!$this->all_time) {
            return Customer::registeredInDateRange($this->date_start, $this->date_end)
                ->count();
        }
        return Customer::count();
    }

    private function ordersCompleted()
    {
        if (!$this->all_time) {
            return Order::completedInDateRange($this->date_start, $this->date_end)
                ->count();
        }
        return Order::status('completed')
            ->count();
    }

    private function customersWithCompletedOrders()
    {
        if (!$this->all_time) {
            return Customer::whereHas('orders', fn ($qry) => $qry->completedInDateRange($this->date_start, $this->date_end))
                ->count();
        }
        return Customer::whereHas('orders', fn ($qry) => $qry->status('completed'))
            ->count();
    }

    private function productsHandedOut()
    {
        if (!$this->all_time) {
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

    public function getStartDateFormattedProperty()
    {
        return (new Carbon($this->date_start))->isoFormat('LL');
    }

    public function getEndDateFormattedProperty()
    {
        return (new Carbon($this->date_end))->isoFormat('LL');
    }
}
