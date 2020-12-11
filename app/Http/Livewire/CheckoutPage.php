<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Component;
use Countries;
use Illuminate\Support\Facades\Notification;
use Propaganistas\LaravelPhone\PhoneNumber;

class CheckoutPage extends Component
{
    public Order $order;
    public array $basket;
    public bool $submitted = false;
    public Collection $countries;
    public string $phone = '';
    public string $phone_country;

    protected $rules = [
        'order.customer_name' => 'required',
        'order.customer_id_number' => 'required',
        'phone' => [
            'required',
            'phone:phone_country,mobile',
        ],
        'phone_country' => 'required_with:phone',
        'order.remarks' => 'nullable',
    ];

    protected $validationAttributes = [
        'order.customer_name' => 'name',
        'order.customer_id_number' => 'ID number',
        'phone' => 'phone number',
        'order.remarks' => 'remarks',
    ];

    public function mount()
    {
        $this->basket = session()->get('basket', []);
        if (count($this->basket) == 0) {
            return redirect()->route('shop-front');
        }

        $this->order = new Order();

        $this->countries = collect(Countries::getList(app()->getLocale()));
        $this->phone_country = setting()->get('order.default_phone_country', '');
    }

    public function render()
    {
        return view('livewire.checkout-page')
            ->layout(null, ['title' => 'Checkout']);
    }

    public function submit(Request $request)
    {
        $this->validate();

        $this->order->customer_phone = PhoneNumber::make($this->phone, $this->phone_country)
            ->formatE164();

        $this->order->customer_ip_address = $request->ip();
        $this->order->customer_user_agent = $request->server('HTTP_USER_AGENT');
        $this->order->locale = app()->getLocale();
        $this->order->save();

        collect($this->basket)
            ->filter(fn ($amount) => $amount > 0)
            ->each(function ($amount, $id) {
                $this->order->products()->attach($id, ['amount' => $amount]);
            });

        $this->order->notify(new OrderRegistered($this->order));
        Notification::send(User::all(), new OrderRegistered($this->order));

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
