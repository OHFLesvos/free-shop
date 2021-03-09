<?php

namespace App\Http\Livewire;

use App;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\CurrentCustomer;
use App\Support\ShoppingBasket;
use Illuminate\Support\Collection;
use Livewire\Component;

class ShopFrontPage extends Component
{
    public Collection $products;
    public Collection $categories;
    public ?Customer $customer = null;
    public bool $shopDisabled;
    public bool $maxOrdersReached = false;

    public function mount(CurrentCustomer $currentCustomer)
    {
        $this->shopDisabled = setting()->has('shop.disabled');

        if (setting()->has('shop.max_orders_per_day') && setting()->get('shop.max_orders_per_day') > 0) {
            $currentOrderCount = Order::whereDate('created_at', today())
                ->where('status', '!=', 'cancelled')
                ->count();
            if (setting()->get('shop.max_orders_per_day') <= $currentOrderCount) {
                $this->maxOrdersReached = true;
            }
        }

        $this->customer = $currentCustomer->get();

        $this->products = Product::query()
            ->available()
            ->orderBy('category->' . App::getLocale())
            ->orderBy('sequence')
            ->orderBy('name->' . App::getLocale())
            ->get()
            ->filter(fn ($product) => $product->quantity_available_for_customer > 0);

        $this->categories = $this->products->values()
            ->sortBy('category')
            ->pluck('category')
            ->unique()
            ->values();
    }

    public function render(ShoppingBasket $basket)
    {
        return view('livewire.shop-front-page', [
                'basket' => $basket->items(),
                'nextOrderIn' => optional($this->customer)->getNextOrderIn(),
            ])
            ->layout(null, ['title' => __('Choose your items')]);
    }

    public function add(ShoppingBasket $basket, $productId, $quantity = 1)
    {
        $basket->add($productId, $quantity);
    }
}
