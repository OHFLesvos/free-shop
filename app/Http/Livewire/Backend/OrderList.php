<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $completed = false;

    public function mount()
    {
        $this->search = session()->get('orders.search', '');
        $this->completed = (bool)session()->get('orders.completed', false);
    }

    public function render()
    {
        session()->put('orders.search', $this->search);
        session()->put('orders.completed', $this->completed);

        return view('livewire.backend.order-list', [
            'orders' => Order::query()
                ->when($this->completed, fn ($qry) => $qry->completed(),  fn ($qry) => $qry->open())
                ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
                ->when($this->completed, fn ($qry) => $qry->orderBy('completed_at', 'desc'), fn ($qry) => $qry->orderBy('created_at', 'desc'))
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
