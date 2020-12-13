<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderRegistered;
use App\Support\ShoppingBasket;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Component;
use Countries;
use Illuminate\Support\Facades\Notification;
use Propaganistas\LaravelPhone\PhoneNumber;

class CheckoutPage extends Component
{
    public Order $order;
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

    // protected function rules() {
    //     $products = $this->products ?? Product::available()->get();
    //     $rules = [];
    //     foreach ($products as $product) {
    //         $rules['basket.' . $product->id] = [
    //             'integer',
    //             'min:0',
    //             'max:' . $product->quantity_available_for_customer,
    //         ];
    //     }
    //     return $rules;
    // }

    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName);
    // }

    public function mount()
    {
        $this->order = new Order();
        $this->countries = collect(Countries::getList(app()->getLocale()));
        $this->phone_country = setting()->get('order.default_phone_country', '');
    }

    public function render(ShoppingBasket $basket)
    {
        return view('livewire.checkout-page', [
                'basket' => $basket->items(),
            ])
            ->layout(null, ['title' => __('Checkout')]);
    }

    public function submit(Request $request, ShoppingBasket $basket)
    {
        $this->validate();

        $this->order->customer_phone = PhoneNumber::make($this->phone, $this->phone_country)
            ->formatE164();

        $this->order->customer_ip_address = $request->ip();
        $this->order->customer_user_agent = $request->server('HTTP_USER_AGENT');
        $this->order->locale = app()->getLocale();
        $this->order->save();

        $this->order->products()->sync($basket->items()
            ->mapWithKeys(fn ($quantity, $id) => [$id => ['quantity' => $quantity]]));

        $this->order->notify(new OrderRegistered($this->order));
        Notification::send(User::all(), new OrderRegistered($this->order));

        $this->submitted = true;

        $basket->empty();
    }

    public function restart(ShoppingBasket $basket)
    {
        $basket->empty();

        return redirect()->route('shop-front');
    }
}
