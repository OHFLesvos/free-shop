<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyOrdersPage extends Component
{
    public Customer $customer;

    public $requestCancel = 0;

    public function mount()
    {
        $this->customer = Auth::guard('customer')->user();
    }

    public function render()
    {
        $orders = $this->customer->orders()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.my-orders-page', [
                'orders' => $orders,
            ])
            ->layout(null, ['title' => __('Find your order')]);
    }

    public function cancelOrder($id)
    {
        $order = $this->customer->orders()->find($id);
        if ($order != null) {
            $order->status = 'cancelled';
            $order->save();
        }
    }
}
