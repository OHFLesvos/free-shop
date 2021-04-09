<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class MyOrdersPage extends FrontendPage
{
    public Customer $customer;

    public $requestCancel = 0;

    protected function title()
    {
        return __('Find your order');
    }

    public function mount()
    {
        $this->customer = Auth::guard('customer')->user();
    }

    public function render()
    {
        $orders = $this->customer->orders()
            ->orderBy('created_at', 'desc')
            ->get();

        return parent::view('livewire.my-orders-page', [
            'orders' => $orders,
        ]);
    }

    public function cancelOrder($id)
    {
        $order = $this->customer->orders()->find($id);
        if ($order != null  && $order->status == 'new') {
            $order->status = 'cancelled';
            $order->save();
            $order->customer->credit += $order->costs;
            $order->customer->save();
        }
    }
}
