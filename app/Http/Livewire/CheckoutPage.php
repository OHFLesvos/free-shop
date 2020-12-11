<?php

namespace App\Http\Livewire;

use App\Events\OrderSubmitted;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Livewire\Component;

class CheckoutPage extends Component
{
    public Order $order;
    public array $basket;
    public bool $submitted = false;

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
        $this->basket = session()->get('basket', []);
        if (count($this->basket) == 0) {
            return redirect()->route('shop-front');
        }

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
        $this->order->save();

        collect($this->basket)
            ->filter(fn ($amount) => $amount > 0)
            ->each(function ($amount, $id) {
                $this->order->products()->attach($id, ['amount' => $amount]);
            });

        OrderSubmitted::dispatch($this->order);

        $this->submitted = true;

        session()->forget('basket');
    }

    public function restart()
    {
        session()->forget('basket');

        return redirect()->route('shop-front');
    }

    public function getBasketContentsProperty()
    {
        return collect($this->basket)
            ->filter(fn ($amount) => $amount > 0)
            ->map(fn ($amount, $id) => [
                'name' => Product::where('id', $id)->first()->name,
                'amount' => $amount,
            ]);
    }
}
