<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderRegisterPage extends BackendPage
{
    use AuthorizesRequests;

    public Customer $customer;

    public Order $order;

    protected function rules(): array
    {
        return [
            'order.remarks' => 'nullable',
        ];
    }

    protected function title(): string
    {
        return 'Register new Order';
    }

    public function mount(): void
    {
        $this->authorize('create', Order::class);

        if (!isset($this->order)) {
            $this->order = new Order();
        }
    }

    public function render(): View
    {
        return parent::view('livewire.backend.order-register-page');
    }

    public function submit(Request $request)
    {
        $this->authorize('create', Order::class);

        $totalPrice = 1;

        $this->order->fill([
            'costs' => $totalPrice,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->order->customer()->associate($this->customer);
        $this->order->save();

        return redirect()->route('backend.orders.show', $this->order);
    }
}
