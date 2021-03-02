<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportsPage extends BackendPage
{
    use AuthorizesRequests;

    protected $title = 'Reports';

    public string $date_start;
    public string $date_end;

    public function mount()
    {
        $this->authorize('view reports');

        $this->date_start = now()->subDays(30)->toDateString();
        $this->date_end = now()->toDateString();
    }

    public function render()
    {
        return parent::view('livewire.backend.reports-page', [
            'ordersCompletedInDateRange' => Order::completedInDateRange($this->date_start, $this->date_end)
                ->count(),
            'customersRegisteredInDateRange' => Customer::registeredInDateRange($this->date_start, $this->date_end)
                ->count(),
            'productsHandedOutInDateRange' => Order::completedInDateRange($this->date_start, $this->date_end)
                ->get()
                ->map(fn ($order) => $order->numberOfProducts())
                ->sum(),

            'productsAvailableCurrently' => Product::available()
                ->count(),
            'ordersInProgress' => Order::open()
                ->count(),

            'customersRegistered' => Customer::count(),
            'ordersCompletedInTotal' => Order::status('completed')
                ->count(),
            'customersWithCompletedOrdersInTotal' => Customer::whereHas('orders', fn ($qry) => $qry->status('completed'))
                ->count(),
            'productsHandedOut' => Order::status('completed')
                ->get()
                ->map(fn ($order) => $order->numberOfProducts())
                ->sum(),
        ]);
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
