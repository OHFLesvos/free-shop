<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public Order $order;

    protected $listeners = [
        '$refresh'
    ];

    public function render()
    {
        return view('livewire.backend.order-history', [
            'audits' => $this->order
                ->audits()
                ->with('user')
                ->paginate(10),
        ]);
    }
}
