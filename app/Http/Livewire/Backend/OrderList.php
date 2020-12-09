<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use Livewire\Component;

class OrderList extends Component
{
    public string $search = '';

    public function render()
    {
        return view('livewire.backend.order-list', [
            'orders' => Order::query()
                ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
                ->open()
                ->orderBy('created_at', 'desc')
                ->get(),
            ])
            ->layout('layouts.backend', ['title' => 'Orders']);
    }
}
