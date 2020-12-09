<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Illuminate\Http\Request;
use Livewire\Component;

class CheckoutPage extends Component
{
    public Order $order;

    protected $rules = [
        'order.customer_name' => 'required',
        'order.customer_id_number' => 'required',
        'order.customer_phone' => 'required',
        'order.remarks' => 'nullable',
    ];

    protected $validationAttributes = [
        'order.customer_name' => 'name',
        'order.customer_id_number' => 'ID number',
        'order.customer_phone' => 'phone number',
        'order.remarks' => 'remarks',
    ];

    public function mount()
    {
        $this->order = new Order();
    }

    public function render()
    {
        return view('livewire.checkout-page')
            ->layout(null, ['title' => 'Checkout']);
    }

    public function submit(Request $request)
    {
        $this->validate();

        $this->order->customer_ip_address = $request->ip();
        $this->order->customer_user_agent = $request->server('HTTP_USER_AGENT');

        dump($this->order);
    }
}
