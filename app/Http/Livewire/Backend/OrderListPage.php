<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\WithPagination;

class OrderListPage extends BackendPage
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = 'new';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->search = request()->input('search', session()->get('orders.search', '')) ?? '';
        $this->status = session()->get('orders.status', 'new');
    }

    protected $title = 'Orders';

    public function render()
    {
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
        session()->put('orders.status', $value);
    }
}
