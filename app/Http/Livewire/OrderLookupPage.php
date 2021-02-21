<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\User;
use App\Notifications\OrderCancelled;
use App\Services\CurrentCustomer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class OrderLookupPage extends Component
{
    public Customer $customer;

    public $requestCancel = 0;

    public function mount(CurrentCustomer $currentCustomer)
    {
        $this->customer = $currentCustomer->get();
    }

    public function render()
    {
        $orders = $this->customer->orders()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.order-lookup-page', [
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

            try {
                Notification::send(User::notifiable()->get(), new OrderCancelled($order));
            } catch (\Exception $ex) {
                Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
            }
        }
    }
}
