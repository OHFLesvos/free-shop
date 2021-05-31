<?php

namespace App\Http\Livewire;

use App\Actions\CancelOrder;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        if ($order != null && $order->isCancellable) {
            CancelOrder::run($order);
            Log::info('Customer cancelled order.', [
                'event.kind' => 'event',
                'event.outcome' => 'success',
                'customer.name' => $order->customer->name,
                'customer.id_number' => $order->customer->id_number,
                'customer.phone' => $order->customer->phone,
                'customer.credit' => $order->customer->credit,
                'order.id' => $order->id,
                'order.costs' => $order->costs,
            ]);
        }
    }
}
