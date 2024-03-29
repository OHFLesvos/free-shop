<?php

namespace App\Http\Livewire;

use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderRegistered;
use App\Services\ShoppingBasket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutPage extends FrontendPage
{
    public ?Order $order = null;

    public Customer $customer;

    public string $remarks = '';

    protected array $rules = [
        'remarks' => 'nullable',
    ];

    protected function title(): string
    {
        return __('Checkout');
    }

    public function mount(): void
    {
        $this->customer = Auth::guard('customer')->user();
    }

    public function render(ShoppingBasket $basket): View
    {
        return parent::view('livewire.checkout-page', [
            'basket' => $basket,
            'nextOrderIn' => $this->customer->getNextOrderIn(),
        ]);
    }

    public function submit(Request $request, ShoppingBasket $basket)
    {
        $this->validate();

        if ($basket->items()->isEmpty()) {
            Log::warning('Customer tried to place empty order.', [
                'event.kind' => 'event',
                'event.outcome' => 'failure',
                'customer.name' => $this->customer->name,
                'customer.id_number' => $this->customer->id_number,
                'customer.phone' => $this->customer->phone,
                'customer.credit' => $this->customer->credit,
            ]);
            session()->flash('error', __('No products have been selected.'));

            return redirect()->route('shop-front');
        }

        $totalPrice = $basket->items()
            ->map(fn ($quantity, $productId) => Product::find($productId)->price * $quantity)
            ->sum();
        if ($this->customer->credit < $totalPrice) {
            Log::warning('Customer did not have enough credit to place order.', [
                'event.kind' => 'event',
                'event.outcome' => 'failure',
                'customer.name' => $this->customer->name,
                'customer.id_number' => $this->customer->id_number,
                'customer.phone' => $this->customer->phone,
                'customer.credit' => $this->customer->credit,
            ]);
            session()->flash('error', __('Not enough credit.'));

            return;
        }

        $order = new Order();
        $order->fill([
            'costs' => $totalPrice,
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

        $this->customer->credit = max(0, $this->customer->credit - $totalPrice);
        $this->customer->save();

        Log::info('Customer placed order.', [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => $this->customer->name,
            'customer.id_number' => $this->customer->id_number,
            'customer.phone' => $this->customer->phone,
            'customer.credit' => $this->customer->credit,
            'order.id' => $order->id,
            'order.costs' => $order->costs,
        ]);

        if (! setting()->has('customer.skip_order_registered_notification')) {
            try {
                $this->customer->notify(new OrderRegistered($order));
            } catch (PhoneNumberBlockedByAdminException $ex) {
                session()->flash('error', __('The phone number :phone has been blocked by an administrator.', ['phone' => $ex->getPhone()]));
            } catch (\Exception $ex) {
                Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
            }
        }

        $this->order = $order;

        $basket->clear();
    }
}
