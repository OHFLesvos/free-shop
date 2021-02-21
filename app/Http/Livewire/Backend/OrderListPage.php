<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use App\Notifications\OrderCancelled;
use App\Notifications\OrderReadyed;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class OrderListPage extends BackendPage
{
    use WithPagination;
    use AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public array $selectedItems = [];
    public bool $selectedAllItems = false;

    public function mount()
    {
        $this->authorize('viewAny', Order::class);

        $this->search = request()->input('search', session()->get('orders.search', '')) ?? '';
        $this->status = request()->input('status', session()->get('orders.status', '')) ?? '';

        if (session()->has('orders.page')) {
            $this->setPage(session()->get('orders.page'));
        }
    }

    protected $title = 'Orders';

    public function render()
    {
        session()->put('orders.page', $this->resolvePage());

        return parent::view('livewire.backend.order-list-page', [
            'orders' => Order::query()
                ->when(in_array($this->status, Order::STATUSES), fn ($qry) => $qry->status($this->status))
                ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
                ->when(in_array($this->status, ['completed', 'cancelled']),
                    fn ($qry) => $qry->orderBy('updated_at', 'desc'),
                    fn ($qry) => $qry->orderBy('created_at', 'asc')
                )
                ->paginate(10),
            ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedItems = [];
    }

    public function updatingStatus()
    {
        $this->resetPage();
        $this->selectedItems = [];
    }

    public function updatingSelectedItems($value)
    {
        info('updating', $value);
    }

    public function updatingSelectedAllItems($value)
    {
        if ($value) {

        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSearch($value)
    {
        session()->put('orders.search', $value);
    }

    public function updatedStatus($value)
    {
        if (filled($value)) {
            session()->put('orders.status', $value);
        } else {
            session()->forget('orders.status');
        }
    }

    public function bulkChange(string $newStatus)
    {
        $this->authorize('update orders');

        $updated = 0;
        foreach ($this->selectedItems as $id) {
            $order = Order::find($id);
            if (Auth::user()->can('update', $order) && $order->status != $newStatus) {

                if ($newStatus == 'completed') {
                    foreach ($order->products as $product) {
                        if ($product->stock < $product->pivot->quantity) {
                            session()->flash('error', 'Cannot complete order; missing ' . abs($product->pivot->quantity - $product->stock) . ' ' . $product->name . ' in stock.');
                            return;
                        }
                    }
                    foreach ($order->products as $product) {
                        $product->stock -= $product->pivot->quantity;
                        $product->save();
                    }
                }

                $order->status = $newStatus;

                if ($order->customer != null) {
                    try {
                        if ($order->status == 'ready') {
                            $order->customer->notify(new OrderReadyed($order));
                        } else if ($order->status == 'cancelled') {
                            // TODO handle cancelled calculations of credits
                            $order->customer->notify(new OrderCancelled($order));
                        }
                    } catch (\Exception $ex) {
                        Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
                    }
                }

                $order->save();
                $updated++;
            }
        }
        session()->flash('message', 'Updated ' . $updated . ' orders.');

        $this->selectedItems = [];
    }
}
