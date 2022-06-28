<?php

namespace App\Http\Livewire;

use App\Actions\CancelOrder;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyOrdersPage extends FrontendPage
{
    public Customer $customer;

    public int $requestCancel = 0;

    protected function title(): string
    {
        return __('Find your order');
    }

    public function mount(): void
    {
        $this->customer = Auth::guard('customer')->user();
    }

    public function render(): View
    {
        $orders = $this->customer->orders()
            ->orderBy('created_at', 'desc')
            ->get();

        return parent::view('livewire.my-orders-page', [
            'orders' => $orders,
        ]);
    }

    public function cancelOrder(int $id): void
    {
        /** @var Order $order */
        $order = $this->customer->orders()->find($id);

        if ($order != null && $order->isCancellable) {
            CancelOrder::run($order);
        }
    }
}
