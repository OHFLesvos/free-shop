<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Notifications\CustomerOrderRegistered;
use App\Notifications\OrderRegistered;
use App\Support\ShoppingBasket;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Facades\Notification;
use Propaganistas\LaravelPhone\PhoneNumber;

class CheckoutPage extends Component
{
    public ?Order $order = null;

    public string $customer_name = '';
    public string $customer_id_number = '';
    public string $customer_phone = '';
    public string $customer_phone_country;
    public string $remarks = '';

    protected $rules = [
        'customer_name' => 'required',
        'customer_id_number' => 'required',
        'customer_phone' => [
            'required',
            'phone:customer_phone_country,mobile',
        ],
        'customer_phone_country' => 'required_with:customer_phone',
        'remarks' => 'nullable',
    ];

    protected $validationAttributes = [
        'customer_name' => 'name',
        'customer_id_number' => 'ID number',
        'customer_phone' => 'phone number',
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
        $this->customer_phone_country = setting()->get('order.default_phone_country', '');
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

        $name = trim($this->customer_name);
        $id_number = trim($this->customer_id_number);
        $phone = PhoneNumber::make($this->customer_phone, $this->customer_phone_country)
            ->formatE164();

        $customer = Customer::firstOrCreate([
            'name' => $name,
            'id_number' => $id_number,
            'phone' => $phone,
        ],[
            'name' => $name,
            'id_number' => $id_number,
            'phone' => $phone,
            'locale' => app()->getLocale(),
        ]);

        $order = new Order();
        $order->fill([
            'customer_id' => $customer->id,
            'remarks' => trim($this->remarks),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        $order->customer()->associate($customer);
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

        $customer->notify(new CustomerOrderRegistered($order));
        Notification::send(User::notifiable()->get(), new OrderRegistered($order));

        $this->order = $order;

        $basket->empty();
    }

    public function restart(ShoppingBasket $basket)
    {
        $basket->empty();

        return redirect()->route('shop-front');
    }
}
