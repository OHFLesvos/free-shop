<?php

namespace App\Http\Livewire\Backend;

use App\Actions\CompleteOrder;
use App\Actions\ReadyOrder;
use App\Actions\RejectOrder;
use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class OrderListPage extends BackendPage
{
    use WithPagination;
    use AuthorizesRequests;
    use WithSorting;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public array $selectedItems = [];
    public bool $selectedAllItems = false;

    public string $sortBy = 'created_at';
    public string $sortDirection = 'asc';
    protected $sortableFields = [
        'id',
        'created_at',
    ];

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
                ->orderBy($this->sortBy, $this->sortDirection)
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

    public function updatingSelectedAllItems($value)
    {
        if ($value) {
            $this->selectedItems = explode(',', $value);
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
                try {
                    if ($newStatus == 'cancelled') {
                        RejectOrder::run($order);
                    } elseif ($newStatus == 'ready') {
                        ReadyOrder::run($order);
                    } elseif ($newStatus == 'completed') {
                        CompleteOrder::run($order);
                    }
                } catch (\Twilio\Exceptions\TwilioException | PhoneNumberBlockedByAdminException $ex) {
                    Log::warning('[' . get_class($ex) . '] Unable to notify customer about order change: ' . $ex->getMessage());
                } catch (\Exception $ex) {
                    session()->flash('error', $ex->getMessage());
                    return;
                }
                $updated++;
            }
        }
        session()->flash('message', 'Updated ' . $updated . ' orders.');

        $this->selectedItems = [];
    }
}
