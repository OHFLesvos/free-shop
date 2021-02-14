<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
}
