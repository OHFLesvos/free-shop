<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderListPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = 'open';

    public function mount()
    {
        $this->search = session()->get('orders.search', '');
        $this->status = session()->get('orders.status', 'open');
    }

    public function render()
    {
        session()->put('orders.search', $this->search);
        session()->put('orders.status', $this->status);

        return view('livewire.backend.order-list-page', [
            'orders' => Order::query()
                ->when($this->status == 'open', fn ($qry) => $qry->open())
                ->when($this->status == 'completed', fn ($qry) => $qry->completed())
                ->when($this->status == 'cancelled', fn ($qry) => $qry->cancelled())
                ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
                ->when($this->status != 'open', fn ($qry) => $qry->orderBy('updated_at', 'desc'), fn ($qry) => $qry->orderBy('created_at', 'desc'))
                ->paginate(10),
            ])
            ->layout('layouts.backend', ['title' => 'Orders']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showOrder($id)
    {
        return redirect()->route('backend.orders.show', $id);
    }
}
