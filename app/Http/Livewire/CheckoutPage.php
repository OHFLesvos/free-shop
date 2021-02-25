<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderRegistered;
use App\Services\CurrentCustomer;
use App\Support\ShoppingBasket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Facades\Notification;

class CheckoutPage extends Component
{
    public ?Order $order = null;
    public Customer $customer;
    public string $remarks = '';

    protected $rules = [
        'remarks' => 'nullable',
    ];

    public function mount(CurrentCustomer $currentCustomer)
    {
        $this->customer = $currentCustomer->get();
    }

    public function render(ShoppingBasket $basket)
    {
        return view('livewire.checkout-page', [
                'basket' => $basket->items(),
                'nextOrderIn' => $this->customer->getNextOrderIn(),
            ])
            ->layout(null, ['title' => __('Checkout')]);
    }

    public function submit(Request $request, ShoppingBasket $basket)
    {
        $this->validate();

        $order = new Order();
        $order->fill([
            'remarks' => trim($this->remarks),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        $order->customer()->associate($this->customer);
        $order->save();

        $order->products()->sync($basket->items()
            ->mapWithKeys(fn ($quantity, $id) => [$id => [
                'quantity' => $quantity,
            ]]));

        // TODO
        // $totalPrice = $order->products->sum('price');
        // if ($totalPrice > $customer->credit) {
        //     // TODO abort
        // }
        // $customer->credit -= $totalPrice;

        try {
            $this->customer->notify(new OrderRegistered($order));
            Notification::locale(config('app.locale'))->send(User::notifiable()->get(), new OrderRegistered($order));
        } catch (\Exception $ex) {
            Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
        }

        $this->order = $order;

        $basket->empty();
    }
}
